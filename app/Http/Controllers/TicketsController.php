<?php

namespace App\Http\Controllers;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminAnswerEvent;
use App\Events\Admin\AdminTicketEvent;
use App\Events\AnswerEvent;
use App\Events\TicketEvent;
use App\Http\Requests\Tickets\GetTicketRequest;
use App\Http\Requests\Tickets\NewAnswerRequest;
use App\Http\Requests\Tickets\NewTicketRequest;
use App\Http\Requests\Tickets\TicketRequest;
use App\Http\Resources\Tickets\TicketsResource;
use App\Models\Answer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketsController extends BaseController
{
    use HelperTrait;

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function myTickets(TicketRequest $request) :View
    {
        $this->data['active_left_menu'] = 'my_tickets';
        if ($request->has('id')) {
            $this->data['ticket'] = Ticket::query()
                ->where('id',$request->id)
                ->with(['answers.user','user'])
                ->first();
            $this->authorize('owner', $this->data['ticket']);
            Answer::query()->where('ticket_id',$request->id)->update(['read_owner' => 1]);
            return $this->showView('ticket');
        } else return $this->showView('my_tickets');
    }

    public function getTickets(): JsonResponse
    {
        return response()->json(TicketsResource::make([
            'tickets' => Ticket::query()
                ->where('user_id',Auth::id())
                ->select(['id','status','subject','created_at'])
                ->with(['answers'])
                ->orderByDesc('created_at')
                ->paginate(6)
        ])->resolve(), 200);
    }

    public function newTicket(NewTicketRequest $request, ProcessingImage $processingImage): JsonResponse
    {
        $fields = $request->validated();
        $fields['user_id'] = Auth::id();
        $fields['status'] = 0;
        $fields['read_admin'] = 0;
        $fields['read_owner'] = 1;
        $ticket = Ticket::query()->create($fields);
        $fields = $processingImage->handle(
            $request,
            [],
            'image',
            'images/ticket_images/',
            'ticket_image'.$ticket->id
        );
        if (count($fields)) $ticket->update($fields);
        $ticket->load('user.ratings');
        broadcast(new AdminTicketEvent('new_item',$ticket));
        broadcast(new TicketEvent('new_item',$ticket));
        return response()->json(['message' => trans('content.ticket_created'),'ticket' => $ticket],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function newAnswer(NewAnswerRequest $request, ProcessingImage $processingImage): JsonResponse
    {
        $fields = $request->validated();
        $ticket = Ticket::query()
            ->where('id',$request->ticket_id)
            ->select(['id','user_id','status'])
            ->first();
        $this->authorize('owner', $ticket);
        if ($ticket->status) abort(403);
        $fields['user_id'] = Auth::id();
        $fields['read_admin'] = 0;
        $fields['read_owner'] = 1;
        $answer = Answer::query()->create($fields);
        $fields = $processingImage->handle(
            $request,
            [],
            'image',
            'images/ticket_images/',
            'answer_image'.$answer->id
        );
        if (count($fields)) $answer->update($fields);
        $answer->load(['ticket.user','user.ratings']);
        broadcast(new AdminAnswerEvent('new_item',$answer));
        broadcast(new AnswerEvent('new_item',$answer));
        return response()->json(['message' => trans('content.answer_sent')],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function closeTicket(GetTicketRequest $request): JsonResponse
    {
        return $this->changeUserStatus($request->ticket_id,1);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resumeTicket(GetTicketRequest $request): JsonResponse
    {
        return $this->changeUserStatus($request->ticket_id,0);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function changeUserStatus(int $id, int $status): JsonResponse
    {
        $ticket = Ticket::query()->where('id',$id)->first();
        $this->authorize('owner', $ticket);
        $ticket->update([
            'status' => $status,
            'read_admin' => 1,
            'read_owner' => 1,
        ]);
        $ticket->load(['user.ratings','answers']);
        $ticket->refresh();
        broadcast(new AdminTicketEvent('change_item',$ticket));
        broadcast(new TicketEvent('change_item',$ticket));
        return response()->json(['message' => trans('content.ticket_closed')],200);
    }
}
