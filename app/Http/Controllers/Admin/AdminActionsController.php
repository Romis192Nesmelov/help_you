<?php

namespace App\Http\Controllers\Admin;
use App\Actions\ConvertTimestamp;
use App\Events\Admin\AdminActionEvent;
use App\Events\Admin\AdminIncentiveEvent;
use App\Events\Admin\AdminOrderEvent;
use App\Events\IncentivesEvent;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Admin\AdminEditActionRequest;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminActionsController extends AdminBaseController
{
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function actions(Action $action, Partner $partner, $slug=null): View
    {
        if (request()->has('id')) {
            $this->data['users'] = User::query()->select(['id','name','family','phone','email'])->get();
            $this->data['partners'] = Partner::select(['id','name'])->get();
        }
        return $this->getSomething($action, $slug);
    }

    public function getActions(): JsonResponse
    {
        return response()->json([
            'actions' => Action::query()
                ->filtered()
                ->select(['id','name','start','end','rating','partner_id'])
                ->with(['partner'])
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'partners' => Partner::select(['id','name'])->get()
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editAction(
        AdminEditActionRequest $request,
        ConvertTimestamp $convertTimestamp
    ): JsonResponse
    {
        $fields = $request->validated();
        foreach (['start','end'] as $timeField) {
            $fields[$timeField] = $convertTimestamp->handle($fields[$timeField]);
        }

        if ($request->has('id')) {
            $action = Action::query()->where('id',$request->id)->with(['users','actionUsers'])->first();
            $action->update($fields);
            $action->refresh();
            broadcast(new AdminActionEvent('change_item',$action));

            foreach ($action->actionUsers as $incentive) {
                if (!in_array($incentive->user_id, request('users_ids'))) {
                    broadcast(new IncentivesEvent('remove_incentive', $incentive, $incentive->user_id));
                }
            }
        } else {
            $action = Action::query()->create($fields);
            broadcast(new AdminOrderEvent('new_item',$action));
        }

        $lastIncentivesIds = $action->actionUsers->pluck('user_id')->toArray();
        foreach (request('users_ids') as $userId) {
            if (!in_array($userId, $lastIncentivesIds)) {
                $this->createNewIncentive(User::find($userId), $action->id);
            }
        }

        $action->users()->sync(request('users_ids'));
        broadcast(new AdminIncentiveEvent(request('users_ids')));

//        $this->saveCompleteMessage();
//        return redirect()->back();
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $this->validate($request,['id' => 'required|exists:actions,id']);
        $action = Action::query()->where('id',$request->id)->with(['users','actionUsers'])->first();

        foreach ($action->actionUsers as $incentive) {
            broadcast(new IncentivesEvent('remove_incentive', $incentive, $incentive->user_id));
        }
        broadcast(new AdminActionEvent('del_item',$action));

        ActionUser::query()->where('action_id',$request->id)->delete();
        $action->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
