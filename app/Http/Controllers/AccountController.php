<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\ChangePhoneRequest;
use App\Http\Requests\Account\EditAccountRequest;
use App\Http\Requests\Account\GetCodeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends BaseController
{
    use HelperTrait;

    public function account() :View
    {
        return $this->showView('account');
    }

    public function getCode(GetCodeRequest $request): JsonResponse
    {
        $request->validated();
        Auth::user()->code = $this->generatingCode();
        Auth::user()->save();
        return response()->json(['message' => trans('auth.code').': '.Auth::user()->code],200);
    }

    public function changePhone(ChangePhoneRequest $request): JsonResponse
    {
        $request->validated();
        if (Auth::user()->code != $request->code) return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        else {
            Auth::user()->phone = $request->phone;
            Auth::user()->save();
            return response()->json(['message' => trans('auth.phone_has_been_changed'), 'number' => $request->phone],200);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $request->validated();
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
        $bornDate = explode('-',$fields['born']);
        if (
            !$bornDate[0] ||
            !$bornDate[1] ||
            !$bornDate[2] ||
            (int)$bornDate[0] > cal_days_in_month(CAL_GREGORIAN, $bornDate[1], $bornDate[2]) ||
            $bornDate[1] > 12 ||
            $bornDate[2] <= (int)date('Y') - 100 ||
            (int)$bornDate[2] > (int)date('Y') - 18 ||
            ((int)$bornDate[2] == (int)date('Y') - 18 && (int)$bornDate[1] < (int)date('m')) ||
            ((int)$bornDate[2] == (int)date('Y') - 18 && (int)$bornDate[1] == (int)date('m') && (int)$bornDate[0] < (int)date('d'))
        ) response()->json(['errors' => ['born' => [trans('validation.wrong_date')]]], 401);

        $fields = $this->processingImage($request, $fields,'avatar', 'images/avatars/', Auth::id());
        Auth::user()->update($fields);
        return response()->json(['message' => trans('content.save_complete')],200);
    }
}
