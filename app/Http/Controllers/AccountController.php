<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends BaseController
{
    use HelperTrait;

    public function account() :View
    {
        $this->data['account_menu'] = [
            ['icon' => 'icon-mail-read', 'name' => trans('auth.subscriptions'), 'href' => 'subscriptions'],
            ['icon' => 'icon-drawer-out', 'name' => trans('auth.my_requests'), 'href' => 'my_requests'],
            ['icon' => 'icon-lifebuoy', 'name' => trans('auth.my_help'), 'href' => 'my_help'],
            ['icon' => 'icon-gift', 'name' => trans('auth.incentives'), 'href' => 'incentives']
        ];
        return $this->showView('account');
    }

    public function getCode(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|unique:users,phone|'.$this->validationPhone]);
        Auth::user()->code = $this->generateCode();
        Auth::user()->save();
        return response()->json(['message' => trans('auth.code').': '.Auth::user()->code],200);
    }

    public function changePhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|unique:users,phone|'.$this->validationPhone,
            'code' => $this->validationCode
        ]);
        if (Auth::user()->code != $request->code) return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        else {
            Auth::user()->phone = $request->phone;
            Auth::user()->save();
            return response()->json(['message' => trans('auth.phone_has_been_changed'), 'number' => $request->phone],200);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => $this->validationString,
            'password' => $this->validationPasswordConfirmed,
        ]);
        if (!Hash::check($request->old_password, Auth::user()->password))
            return response()->json(['errors' => ['old_password' => [trans('auth.wrong_old_password')]]], 401);
        else {
            Auth::user()->password = Hash::make($request->password);
            Auth::user()->save();
            return response()->json(['message' => trans('auth.password_has_been_changed')],200);
        }
    }

    public function editAccount(Request $request): JsonResponse
    {
        $validationArr = [
            'avatar' => $this->validationJpgAndPng,
            'name' => $this->validationString,
            'family' => $this->validationString,
            'born' => $this->validationBorn,
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => $this->validationPhone,
            'info_about' => $this->validationText
        ];

        $fields = $request->validate($validationArr);

        $bornDate = explode('-',$fields['born']);
        if (
            !$bornDate[1] ||
            !$bornDate[2] ||
            $bornDate[1] > 12 ||
            $bornDate[0] >= (int)date('Y') ||
            (int)$bornDate[2] > cal_days_in_month(CAL_GREGORIAN, $bornDate[1], $bornDate[0]) ||
            (int)$bornDate[0] > (int)date('Y') - 18 ||
            ((int)$bornDate[0] == (int)date('Y') - 18 && (int)$bornDate[1] < (int)date('m')) ||
            ((int)$bornDate[0] == (int)date('Y') - 18 && (int)$bornDate[1] == (int)date('m') && (int)$bornDate[2] < (int)date('d'))
        ) response()->json(['errors' => ['born' => [trans('validation.wrong_date')]]], 401);

        $fields = $this->processingImage($request, $fields,'avatar', 'images/avatars/', Auth::id());
        Auth::user()->update($fields);
        return response()->json(['message' => trans('content.save_complete')],200);
    }
}
