<?php

namespace App\Http\Controllers;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminAnswerEvent;
use App\Events\Admin\AdminTicketEvent;
use App\Events\AnswerEvent;
use App\Events\TicketEvent;
use App\Http\Requests\Tickets\ClosingTicketRequest;
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
            $this->data['ticket'] = Ticket::query()->where('id',$request->id)->with('answers')->first();
            $this->authorize('owner', $this->data['ticket']);
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
        $fields = $processingImage->handle($request, [], 'image', 'images/ticket_images/', 'image'.$ticket->id);
        if (count($fields)) $ticket->update($fields);
        $ticket->load('user.ratings');
        broadcast(new AdminTicketEvent('new_item',$ticket));
        broadcast(new TicketEvent('new_item',$ticket));
        return response()->json(['message' => trans('content.ticket_created'),'ticket' => $ticket],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function newAnswer(NewAnswerRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $ticket = Ticket::where('id',$request->ticket_id)->first();
        $this->authorize('owner', $ticket);
        $fields['user_id'] = Auth::id();
        $fields['read_admin'] = 0;
        $fields['read_owner'] = 1;
        $answer = Answer::query()->create($fields);
        $answer->load(['ticket.user','user.ratings']);
        broadcast(new AdminAnswerEvent('new_item',$answer));
        broadcast(new AnswerEvent('new_item',$answer));
        return response()->json(['message' => trans('content.answer_sent')],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function closeTicket(ClosingTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::query()->where('id',$request->ticket_id)->first();
        $this->authorize('owner', $ticket);
        $ticket->update([
            'status' => 1,
            'read_admin' => 1,
            'read_owner' => 1,
        ]);
        $ticket->load('user.ratings');
        $ticket->refresh();
        broadcast(new AdminTicketEvent('change_item',$ticket));
        broadcast(new TicketEvent('change_item',$ticket));
        return response()->json(['message' => trans('content.ticket_closed')],200);
    }
}
