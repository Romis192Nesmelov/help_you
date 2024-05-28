<?php

namespace App\Http\Controllers\Admin;
use App\Actions\DeleteFile;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminTicketEvent;
use App\Events\TicketEvent;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Admin\AdminEditTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminTicketsController extends AdminBaseController
{
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function tickets(Ticket $ticket, User $user, $slug=null): View
    {
        $this->data['users'] = User::query()->default()->get();
        return $this->getSomething($ticket, $slug, ['answers'], $user);
    }

    public function getTickets(): JsonResponse
    {
        return response()->json([
            'objects' => Ticket::query()
                ->withUserId()
                ->filtered()
                ->with('user.ratings')
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'users' => User::query()->default()->get(),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editTicket(AdminEditTicketRequest $request, ProcessingImage $processingImage): JsonResponse
    {
        $fields = $request->validated();
        $imagePath = 'images/ticket_images/';

        if ($request->has('id')) {
            $ticket = Ticket::query()->where('id',$request->id)->with(['user'])->first();
            $fields = $processingImage->handle($request, $fields, 'image', $imagePath, 'image'.$ticket->id);
            $ticket->update($fields);
            $ticket->refresh();
            broadcast(new AdminTicketEvent('change_item',$ticket));
            if ($ticket->wasChanged()) broadcast(new TicketEvent('change_item',$ticket));
        } else {
            $ticket = Ticket::query()->create($fields);
            $fields = $processingImage->handle($request, [], 'image', $imagePath, 'logo'.$ticket->id);
            if (count($fields)) $ticket->update($fields);
            $ticket->load('user');
            $ticket->refresh();

            broadcast(new AdminTicketEvent('new_item',$ticket));
            broadcast(new TicketEvent('new_item',$ticket));
        }
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteTicket(Request $request, DeleteFile $deleteFile): JsonResponse
    {
        $this->validate($request,['id' => 'required|exists:tickets,id']);
        $ticket = Ticket::query()->where('id',$request->id)->with(['answers'])->first();

        foreach ($ticket->answers as $answer) {
            if ($answer->image) $deleteFile->handle($answer->image);
        }
        broadcast(new AdminTicketEvent('del_item',$ticket));
        broadcast(new TicketEvent('del_item',$ticket));

        $ticket->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
