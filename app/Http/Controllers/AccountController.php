<?php

namespace App\Http\Controllers;

use App\Actions\AuthGeneratingCode;
use App\Actions\ChangeAvatar;
use App\Actions\DeleteFile;
use App\Actions\DeleteSomething;
use App\Actions\ProcessingImage;
use App\Actions\ProcessingSpecialFields;
use App\Actions\SetReadUnread;
use App\Actions\SetReadUnreadUser;
use App\Events\Admin\AdminUserEvent;
use App\Events\IncentivesEvent;
use App\Http\Requests\Account\ChangeAvatarRequest;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\ChangePhoneRequest;
use App\Http\Requests\Account\EditAccountRequest;
use App\Http\Requests\Account\GetCodeRequest;
use App\Http\Requests\Account\IncentivesRequest;
use App\Http\Requests\Account\SubscriptionRequest;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Answer;
use App\Models\Order;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\ReadPerformer;
use App\Models\ReadRemovedPerformer;
use App\Models\ReadStatusOrder;
use App\Models\Subscription;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends BaseController
{
    use HelperTrait;

    public function account() :View
    {
        $this->data['active_left_menu'] = null;
        return $this->showView('account');
    }

    public function getNews(): JsonResponse
    {
        return response()->json([
            'news_subscriptions' => ReadOrder::query()
                ->whereIn('subscription_id',Subscription::query()->default()->pluck('id')->toArray())
                ->where('read',null)
                ->with('order.user')
                ->get(),
            'news_performers' => ReadPerformer::query()
                ->whereIn('order_id',Order::query()->where('user_id',Auth::id())->pluck('id')->toArray())
                ->where('read',null)
                ->with('order')
                ->with('user')
                ->get(),
            'news_removed_performers' => ReadRemovedPerformer::query()
                ->where('user_id', Auth::id())
                ->where('read', null)
                ->with('order')
                ->get(),
            'news_status_orders' => ReadStatusOrder::query()
                ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
                ->where('read',null)
                ->with('order')
                ->get(),
            'news_incentive' => ActionUser::query()
                ->where('user_id', Auth::id())
                ->where('read', null)
                ->where('active', 1)
                ->with('action')
                ->get(),
            'news_tickets' => Ticket::query()
                ->where('status',1)
                ->select(['id','subject'])
                ->where('read_owner',0)
                ->get(),
            'news_answers' => Answer::query()
                ->whereIn('ticket_id',Ticket::query()->where('user_id',Auth::id())->where('status',0)->pluck('id')->toArray())
                ->where('read_owner',0)
                ->select(['id','ticket_id'])
                ->with('ticket')
                ->get()
        ]);
    }

    public function mySubscriptions() :View
    {
        $this->data['active_left_menu'] = 'my_subscriptions';
        return $this->showView('my_subscriptions');
    }

    public function getMyUnreadSubscriptions(): JsonResponse
    {
        $unreadOrdersIds = ReadOrder::query()
            ->whereIn('subscription_id',Subscription::query()->default()->pluck('id')->toArray())
            ->where('read',null)
            ->pluck('order_id')
            ->toArray();

        return response()->json([
            'orders' => Order::query()
                ->whereIn('id',$unreadOrdersIds)
                ->where('status',2)
                ->with('user.ratings')
                ->with('performers.ratings')
                ->with('readSubscriptions.subscription.orders')
                ->with('orderType')
                ->with('subType')
                ->orderByDesc('created_at')
                ->paginate(4)
            ]);
    }

    public function myOrders(SetReadUnread $actionSetReadUnread): View
    {
        $this->setReadUnreadByMyOrders($actionSetReadUnread);
        $this->data['active_left_menu'] = 'my_orders';
        return $this->showView('my_orders');
    }

    public function setReadUnreadByMyOrders(SetReadUnread $actionSetReadUnread): JsonResponse
    {
        $actionSetReadUnread->handle(new ReadStatusOrder());
        $actionSetReadUnread->handle(new ReadPerformer());
        return response()->json(200);
    }

    public function myOrdersArchive(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyOrders(0)],200);
    }

    public function myOrdersActive(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyOrders(1)],200);
    }

    public function myOrdersOpen(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyOrders(2)],200);
    }

    public function myOrdersApproving(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyOrders(3)],200);
    }

    private function getMyOrders(int $status): LengthAwarePaginator
    {
        return Order::query()
            ->where('user_id',Auth::id())
            ->where('status',$status)
            ->with('user.ratings')
            ->with('performers.ratings')
            ->with('orderType')
            ->orderByDesc('created_at')
            ->paginate(4);
    }

    public function myHelp(SetReadUnreadUser $setReadUnreadUser): View
    {
        $setReadUnreadUser->handle(new ReadPerformer());
        $setReadUnreadUser->handle(new ReadRemovedPerformer());
        $this->data['active_left_menu'] = 'my_help';
        return $this->showView('my_help');
    }

    public function incentives(Request $request): View
    {
        $this->data['active_left_menu'] = 'incentives';
        if ($request->has('id')) {
            $this->data['action'] = Action::findOrFail($request->id);
            $incentive = ActionUser::where('action_id',$this->data['action']->id)->where('user_id',Auth::id())->first();
            if (!$incentive) abort(403);
            $incentive->read = 1;
            $incentive->save();
            broadcast(new IncentivesEvent('remove_incentive', $incentive, Auth::id()));
            return $this->showView('action');
        } else return $this->showView('incentives');
    }

    public function getMyIncentives(): LengthAwarePaginator
    {
        return ActionUser::query()
            ->where('user_id',Auth::id())
            ->where('active', 1)
            ->with('actionFull.partner')
            ->paginate(6);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteIncentive(IncentivesRequest $request): JsonResponse
    {
        $incentive = ActionUser::find($request->id);
        $this->authorize('owner', $incentive);
        broadcast(new IncentivesEvent('remove_incentive', $incentive, Auth::id()));
        $incentive->active = 0;
        $incentive->save();
        return response()->json([],200);
    }

    public function myHelpActive(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyHelp(1)],200);
    }

    public function myHelpArchive(): JsonResponse
    {
        return response()->json(['orders' => $this->getMyHelp(0)],200);
    }

    private function getMyHelp(int $status): LengthAwarePaginator
    {
        $orderIds = OrderUser::where('user_id', Auth::id())->pluck('order_id')->toArray();
        return Order::query()
            ->whereIn('id',$orderIds)
            ->where('status',$status)
            ->with('user.ratings')
            ->with('performers.ratings')
            ->with('orderType')
            ->orderByDesc('created_at')
            ->paginate(4);
    }

    public function getCode(GetCodeRequest $request, AuthGeneratingCode $authGeneratingCode): JsonResponse
    {
        Auth::user()->code = $authGeneratingCode->handle();
        Auth::user()->save();
        return response()->json(['message' => trans('auth.code').': '.Auth::user()->code],200);
    }

    public function changePhone(ChangePhoneRequest $request): JsonResponse
    {
        if (Auth::user()->code != $request->code) return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        else {
            Auth::user()->phone = $request->phone;
            Auth::user()->save();

            broadcast(new AdminUserEvent('change_item',Auth::user()));
            return response()->json(['message' => trans('auth.phone_has_been_changed'), 'number' => $request->phone],200);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        if (!Hash::check($request->old_password, Auth::user()->password))
            return response()->json(['errors' => ['old_password' => [trans('auth.wrong_old_password')]]], 401);
        else {
            Auth::user()->password = Hash::make($request->password);
            Auth::user()->save();
            return response()->json(['message' => trans('auth.password_has_been_changed')],200);
        }
    }

    public function changeAvatar(ChangeAvatarRequest $request, ProcessingImage $processingImage, ChangeAvatar $changeAvatar): JsonResponse
    {
        return $changeAvatar->handle($request, $processingImage);
    }

    public function editAccount(EditAccountRequest $request, ProcessingSpecialFields $processingSpecialFields): JsonResponse
    {
        $fields = $request->validated();
        $fields = $processingSpecialFields->handle($fields, 'mail_notice');
        $birthday = Carbon::parse($fields['born']);
        $currentDate = Carbon::now();
        $age = $currentDate->diffInYears($birthday);
        if ($age < 18 || $age > 100) return response()->json(['errors' => ['born' => [trans('validation.wrong_date')]]], 401);
        Auth::user()->update($fields);
        broadcast(new AdminUserEvent('change_item',Auth::user()));
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function subscription(SubscriptionRequest $request): JsonResponse
    {
        if (Auth::id() == $request->user_id) return response()->json([],403);
        $subscription = Subscription::query()->where('subscriber_id',Auth::id())->where('user_id',$request->user_id)->first();
        if ($subscription) {
            $subscription->delete();
            $subscriptionExist = false;
        } else {
            Subscription::create([
                'subscriber_id' => Auth::id(),
                'user_id' => $request->user_id
            ]);
            $subscriptionExist = true;
        }
        return response()->json(['subscription' => $subscriptionExist],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteSubscription(Request $request, DeleteFile $deleteFile, DeleteSomething $deleteSomething, Subscription $subscription): JsonResponse
    {
        return $deleteSomething->handle($request, $deleteFile, $subscription);
    }
}
