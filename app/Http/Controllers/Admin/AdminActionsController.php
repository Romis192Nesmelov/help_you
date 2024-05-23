<?php

namespace App\Http\Controllers\Admin;
use App\Actions\ProcessingSpecialFields;
use App\Events\Admin\AdminActionEvent;
use App\Events\Admin\AdminOrderEvent;
use App\Events\Admin\AdminUserEvent;
use App\Events\IncentivesEvent;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Admin\AdminEditActionRequest;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminActionsController extends AdminBaseController
{
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function actions(Action $action, Partner $partner, $slug=null): View
    {
        $this->data['partners'] = Partner::select(['id','name'])->get();
        return $this->getSomething($action, $slug);
    }

    public function getActions(): JsonResponse
    {
        return response()->json([
            'actions' => Action::query()
                ->filtered()
                ->select(['name','start','end','rating','partner_id'])
                ->with('partner')
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
        ProcessingSpecialFields $processingSpecialFields,
    ): RedirectResponse
    {
        $fields = $request->validated();
        $fields = $processingSpecialFields->handle($fields, 'active');

        if ($request->has('id')) {
            $action = Action::query()->create($fields);
            $action = $action->users->sync(request('user_ids'));
            broadcast(new AdminOrderEvent('new_item',$action));
        } else {
            $action = Action::query()->where('id',$request->id)->with(['users','actionUsers'])->first();
            $pastIncentivesIds = $action->actionUsers->pluck('user_id')->toArray();

            foreach ($action->actionUsers as $incentive) {
                if (!in_array($incentive->user_id, request('user_ids'))) {
                    broadcast(new AdminUserEvent('del_item', $incentive->user));
                    broadcast(new IncentivesEvent('remove_incentive', $incentive, $incentive->user_id));
                }
            }

            $action = $action->users->sync(request('user_ids'));
            $action->refresh();

            foreach ($action->actionUsers as $incentive) {
                if (!in_array($incentive->user_id, $pastIncentivesIds)) {
                    broadcast(new AdminUserEvent('new_item',$incentive->user));
                    broadcast(new IncentivesEvent('new_incentive', $incentive, $incentive->user_id));
                }
            }

            $action->update($fields);
            broadcast(new AdminOrderEvent('change_item',$action));
        }

        $this->saveCompleteMessage();
        return redirect()->back();
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
            broadcast(new AdminUserEvent('del_item', $incentive->user));
        }
        broadcast(new AdminActionEvent('del_item',$action));

        ActionUser::query()->where('action_id',$request->id)->delete();
        $action->delete();
        return response()->json([],200);
    }
}
