<?php

namespace App\Http\Controllers\Admin;
use App\Actions\DeleteFile;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminAnswerEvent;
use App\Events\AnswerEvent;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Admin\AdminEditAnswerRequest;
use App\Models\Answer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminAnswersController extends AdminBaseController
{
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function answers(Answer $answer, Ticket $ticket, User $user, $slug=null): View|RedirectResponse
    {
        if (request('id') && !request('parent_id')) {
            $answer = Answer::query()->where('id',request('id'))->select('ticket_id')->first();
            return redirect(route('admin.answers',['id' => request('id'), 'parent_id' => $answer->ticket_id]));
        } else return $this->getSomething($answer, $slug, [], $ticket, $user, 'users');
    }

    public function getAnswers(): JsonResponse
    {
        return response()->json([
            'items' => Answer::query()
                ->withTicketId()
                ->filtered()
                ->with(['ticket','user.ratings'])
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'users' => User::query()->default()->get(),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editAnswer(AdminEditAnswerRequest $request, ProcessingImage $processingImage): JsonResponse
    {
        $fields = $request->validated();
        $imagePath = 'images/ticket_images/';

        if ($request->has('id')) {
            $answer = Answer::query()->where('id',$request->id)->with(['ticket','user.ratings'])->first();
            $fields = $processingImage->handle($request, $fields, 'image', $imagePath, 'image'.$answer->ticket->id.'_answer'.$answer->id);
            $answer->update($fields);
            $answer->load(['ticket','user.ratings']);
            $answer->refresh();

            if ($answer->wasChanged()) {
                broadcast(new AdminAnswerEvent('change_item',$answer));
                broadcast(new AnswerEvent('change_item',$answer));
            }
        } else {
            $answer = Answer::query()->create($fields);
            $fields = $processingImage->handle($request, [], 'image', $imagePath, 'image'.$answer->ticket->id.'_answer'.$answer->id);
            if (count($fields)) $answer->update($fields);
            $answer->load(['ticket','user.ratings']);
            $answer->refresh();

            broadcast(new AdminAnswerEvent('new_item',$answer));
            broadcast(new AnswerEvent('new_item',$answer));
        }

        if ($answer->ticket->status !== 0) {
            $answer->ticket->status = 0;
            $answer->ticket->save();
        }

        return response()->json(['message' => trans('content.save_complete')],200);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteAnswer(Request $request, DeleteFile $deleteFile): JsonResponse
    {
        $this->validate($request,['id' => 'required|exists:answers,id']);
        $answer = Answer::query()->where('id',$request->id)->with(['ticket'])->first();
        if ($answer->image) $deleteFile->handle($answer->image);
        broadcast(new AdminAnswerEvent('del_item',$answer));
        broadcast(new AnswerEvent('del_item',$answer));

        $answer->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
