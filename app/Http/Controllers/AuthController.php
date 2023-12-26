<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GenerateCodeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HelperTrait;

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $credentials['active'] = 1;
        $credentials['phone'] = $this->unifyPhone($credentials['phone']);

        if (Auth::attempt($credentials, $request->remember == 'on')) {
            $request->session()->regenerate();
            return response()->json(['account' => Auth::user()->name && Auth::user()->family && Auth::user()->born && Auth::user()->email],200);
        } else return response()->json(['errors' => ['phone' => [trans('auth.failed')], 'password' => [trans('auth.failed')]]], 401);
    }

    public function generateCode(GenerateCodeRequest $request): JsonResponse
    {
        $phone = $this->unifyPhone($request->phone);
        $user = User::where('phone',$phone)->first();
        if (!$user) {
            $user = User::create([
                'phone' => $phone,
                'code' => $this->generatingCode(),
                'active' => 0
            ]);
            return response()->json(['message' => trans('auth.code').': '.$user->code],200);
        } elseif ($user->active) {
            return response()->json(['errors' => ['phone' => [trans('auth.user_with_this_phone_is_already_registered')]]], 400);
        } else {
            $user->code = $this->generatingCode();
            $user->save();
            return response()->json(['message' => trans('auth.code').': '.$user->code],200);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('phone',$this->unifyPhone($request->phone))->first();
        if ($user->code == $request->code) {
            $user->update([
                'password' => bcrypt($credentials['password']),
                'active' => 1
            ]);
            return response()->json(['message' => trans('auth.register_complete')],200);
        } else {
            return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone',$this->unifyPhone($request->phone))->where('active',1)->first();
        if (!$user) return response()->json(['errors' => ['phone' => [trans('auth.wrong_phone')]]], 401);
        else {
            $password = Str::random(5);
            $user->update(['password' => $password]);
            return response()->json(['message' => trans('auth.new_password_has_been_sent_to_your_phone').'<br>'.$password],200);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('home'));
    }
}
