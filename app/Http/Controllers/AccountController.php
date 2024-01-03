<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\ChangePhoneRequest;
use App\Http\Requests\Account\EditAccountRequest;
use App\Http\Requests\Account\GetCodeRequest;
use App\Http\Requests\Account\SubscriptionRequest;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        $this->data['subscriptions'] = Subscription::query()->default()->get();
        $this->data['active_left_menu'] = 'account.my_subscriptions';
        return $this->showView('my_subscriptions');
    }

    public function myOrders(): View
    {
        $this->data['orders'] = [
            'active' => Auth::user()->ordersActiveAndApproving,
            'approving' => Auth::user()->orderApproving,
            'archive' => Auth::user()->ordersArchive
        ];
        $this->data['active_left_menu'] = 'account.my_orders';
        return $this->showView('my_orders');
    }

    public function myHelp(): View
    {
        $this->data['orders'] = [
            'active' => Auth::user()->orderActivePerformer,
            'archive' => Auth::user()->orderArchivePerformer
        ];
        $this->data['active_left_menu'] = 'account.my_help';
        return $this->showView('my_help');
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

    public function editAccount(EditAccountRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields = $this->processingSpecialField($fields, 'mail_notice');
        $birthday = Carbon::parse($fields['born']);
        $currentDate = Carbon::now();
        $age = $currentDate->diffInYears($birthday);
        if ($age < 18 || $age > 100) return response()->json(['errors' => ['born' => [trans('validation.wrong_date')]]], 401);
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
