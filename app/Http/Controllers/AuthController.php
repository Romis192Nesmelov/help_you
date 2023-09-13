<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HelperTrait;

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate(['phone' => $this->validationPhone,'password' => 'required|min:3|max:20']);
        $credentials['active'] = 1;
        if (Auth::attempt($credentials, $request->remember == 'on')) {
            $request->session()->regenerate();
            return response()->json(['code' => 1111],200);
        } else return response()->json(['errors' => ['phone' => [trans('auth.failed')], 'password' => [trans('auth.failed')]]], 401);
    }

    public function generateCode(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone' => $this->validationPhone,
            'password' => $this->validationPasswordConfirmed,
            'i_agree' => 'accepted'
        ]);
        $user = User::where('phone',$request->phone)->first();
        if (!$user) {
            $user = User::create([
                'phone' => $credentials['phone'],
                'code' => $this->getCode(),
                'active' => 0
            ]);
            return response()->json([],200);
        } elseif ($user->active) {
            return response()->json(['errors' => ['phone' => [trans('auth.user_with_this_phone_is_already_registered')]]], 400);
        } else {
            $user->code = $this->getCode();
            $user->save();
            return response()->json(['message' => trans('auth.code').': '.$user->code],200);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone' => $this->validationPhone,
            'password' => $this->validationPasswordConfirmed,
            'code' => $this->validationCode,
            'i_agree' => 'accepted'
        ]);
        $user = User::where('phone',$request->phone)->first();
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

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate(['phone' => $this->validationPhone]);
        $user = User::where('phone',$request->phone)->where('active',1)->first();
        if (!$user) return response()->json(['errors' => ['phone' => [trans('auth.wrong_phone')]]], 401);
        else {
            $password = Str::random(5);
            $user->update(['password' => $password]);
            return response()->json(['message' => trans('auth.new_password_has_been_sent_to_your_phone').'<br>'.$password],200);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([],200);
    }

    private function getCode(): string
    {
        return rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9);
    }
}
