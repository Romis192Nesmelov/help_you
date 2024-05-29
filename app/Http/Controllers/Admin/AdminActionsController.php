<?php

namespace App\Http\Controllers\Admin;
use App\Actions\ConvertTimestamp;
use App\Events\Admin\AdminActionEvent;
use App\Events\Admin\AdminIncentiveEvent;
use App\Events\Admin\AdminOrderEvent;
use App\Events\IncentivesEvent;
use App\Http\Controllers\FieldsHelperTrait;
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
    use FieldsHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function actions(Action $action, Partner $partner, $slug=null): View
    {
        $this->data['users'] = User::query()->default()->get();
        $this->data['partners'] = Partner::query()->select(['id','name'])->get();
        return $this->getSomething($action, $slug, ['users'], $partner);
    }

    public function getActions(): JsonResponse
    {
        return response()->json([
            'items' => Action::query()
                ->withPartnerId()
                ->filtered()
                ->select(['id','name','start','end','rating','partner_id'])
                ->with(['partner'])
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'partners' => Partner::query()->select(['id','name'])->get()
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
        $usersIds = [];
        foreach (explode(',',$fields['users_ids']) as $userId) {
            $usersIds[] = (int)$userId;
        }
        unset($fields['users_ids']);
        foreach (['start','end'] as $timeField) {
            $fields[$timeField] = $convertTimestamp->handle($fields[$timeField]);
        }

        if ($request->has('id')) {
            $action = Action::query()->where('id',$request->id)->with($this->actionLoadFields)->first();
            $action->update($fields);
            if ($action->wasChanged()) broadcast(new AdminActionEvent('change_item',$action));

            foreach ($action->actionUsers as $incentive) {
                if (!in_array($incentive->user_id, $usersIds)) {
                    broadcast(new IncentivesEvent('remove_incentive', $incentive, $incentive->user_id));
                }
            }
        } else {
            $action = Action::query()->create($fields);
            $action->load($this->actionLoadFields);
            broadcast(new AdminOrderEvent('new_item',$action));
        }

        $lastIncentivesIds = $action->actionUsers->pluck('user_id')->toArray();

        foreach ($usersIds as $userId) {
            if (!in_array($userId, $lastIncentivesIds)) {
                $this->createNewIncentive(User::find($userId), $action->id);
            }
        }

        $action->users()->sync($usersIds);
        broadcast(new AdminIncentiveEvent($usersIds));

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
        $action = Action::query()->where('id',$request->id)->with($this->actionLoadFields)->first();

        foreach ($action->actionUsers as $incentive) {
            broadcast(new IncentivesEvent('remove_incentive', $incentive, $incentive->user_id));
        }
        broadcast(new AdminActionEvent('del_item',$action));

        ActionUser::query()->where('action_id',$request->id)->delete();
        $action->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
