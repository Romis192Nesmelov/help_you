<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ChangeAvatarRequest;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\ChangePhoneRequest;
use App\Http\Requests\Account\EditAccountRequest;
use App\Http\Requests\Account\GetCodeRequest;
use App\Http\Requests\Account\SubscriptionRequest;
use App\Models\Order;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\ReadStatusOrder;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

    public function mySubscriptions() :View
    {
        $this->setReadUnread(new ReadOrder());
        $this->data['active_left_menu'] = 'account.my_subscriptions';
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

    public function myOrders(): View
    {
        $this->setReadUnread(new ReadStatusOrder());
        $this->data['active_left_menu'] = 'account.my_orders';
        return $this->showView('my_orders');
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

    public function myHelp(): View
    {
        $this->setReadUnreadRemovedPerformers();
        $this->data['active_left_menu'] = 'account.my_help';
        return $this->showView('my_help');
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

    public function getCode(GetCodeRequest $request): JsonResponse
    {
        Auth::user()->code = $this->generatingCode();
        Auth::user()->save();
        return response()->json(['message' => trans('auth.code').': '.Auth::user()->code],200);
    }

    public function changePhone(ChangePhoneRequest $request): JsonResponse
    {
        if (Auth::user()->code != $request->code) return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        else {
            Auth::user()->phone = $request->phone;
            Auth::user()->save();
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

    public function changeAvatar(ChangeAvatarRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields['avatar_props'] = [];
        foreach (['size','position_x','position_y'] as $avatarProp) {
            $fieldProp = 'avatar_'.$avatarProp;
            $prop = $fields[$fieldProp];
            if ($prop) $fields['avatar_props']['background-'.str_replace('_','-',$avatarProp)] = $avatarProp == 'size' ? ((int)$prop).'%' : ((float)$prop);
            unset($fields[$fieldProp]);
        }
        $fields = $this->processingImage($request, $fields,'avatar', 'images/avatars/', 'avatar'.Auth::id());
        Auth::user()->update($fields);
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function editAccount(EditAccountRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields = $this->processingSpecialField($fields, 'mail_notice');
        $birthday = Carbon::parse($fields['born']);
        $currentDate = Carbon::now();
        $age = $currentDate->diffInYears($birthday);
        if ($age < 18 || $age > 100) return response()->json(['errors' => ['born' => [trans('validation.wrong_date')]]], 401);
        Auth::user()->update($fields);
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function subscription(SubscriptionRequest $request): JsonResponse
    {
        if (Auth::id() == $request->user_id) return response()->json([],403);
        $subscription = Subscription::where('subscriber_id',Auth::id())->where('user_id',$request->user_id)->first();
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
    public function deleteSubscription(Request $request): JsonResponse
    {
        return $this->deleteSomething($request, new Subscription(), 'subscriber');
    }
}
