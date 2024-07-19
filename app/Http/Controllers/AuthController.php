<?php

namespace App\Http\Controllers;

use App\Actions\AuthGeneratingCode;
use App\Actions\UnifyPhone;
use App\Events\ChangePasswordEvent;
use App\Http\Requests\Auth\GenerateCodeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    use HelperTrait;

    public function login(LoginRequest $request, UnifyPhone $actionUnifyPhone): JsonResponse
    {
        $credentials = $request->validated();
        $credentials['active'] = 1;
        $credentials['phone'] = $actionUnifyPhone->handle($credentials['phone']);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return response()->json([
                'id' => Auth::id(),
                'account' => Auth::user()->name && Auth::user()->family && Auth::user()->born && Auth::user()->email
            ],200);
        } else return response()->json(['errors' => ['phone' => [trans('auth.failed')], 'password' => [trans('auth.failed')]]], 401);
    }

    public function generateCode(GenerateCodeRequest $request, AuthGeneratingCode $actionGeneratingCode, UnifyPhone $actionUnifyPhone): JsonResponse
    {
        $phone = $actionUnifyPhone->handle($request->phone);
        $user = User::query()->where('phone', $phone)->first();
        $attempts = $this->getSmsAttempts();

        if (!$user) {
            $user = User::query()->create([
                'phone' => $phone,
                'code' => $actionGeneratingCode->handle(),
                'admin' => 0,
                'active' => 0
            ]);
            event(new Registered($user));
            return response()->json(['message' => trans('auth.sms_sent')], 200);
        } elseif ($user->active) {
            return response()->json(['errors' => ['phone' => [trans('auth.user_with_this_phone_is_already_registered')]]], 400);
        } else if (time() - $user->updated_at->timestamp < 55) {
            return response()->json(['message' => trans('auth.the_minute_has_not_expired')], 403);
        } else if ($attempts >= 3) {
            return response()->json(['message' => trans('auth.too_many_tries_delay')], 403);
        } else if (RateLimiter::tooManyAttempts('register:'.request('email'), $perMinute = 5)) {
            return response()->json(['message' => trans('auth.too_many_tries')], 425);
        } else {
            $this->setSmsAttempts($attempts + 1);
            $user->code = $actionGeneratingCode->handle();
            $user->save();
            event(new Registered($user));
            return response()->json(['message' => trans('auth.sms_sent')],200);
        }
    }

    public function register(RegisterRequest $request, UnifyPhone $actionUnifyPhone): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::query()->where('phone',$actionUnifyPhone->handle($request->phone))->first();

        if ($user->active) {
            return response()->json(['errors' => ['phone' => [trans('auth.user_with_this_phone_is_already_registered')]]], 400);
        } else if ($user->code == $request->code) {
            $user->update([
                'password' => bcrypt($credentials['password']),
                'active' => 1
            ]);
            event(new Verified($user));
            return response()->json(['message' => trans('auth.register_complete')],200);
        } else {
            return response()->json(['errors' => ['code' => [trans('auth.wrong_code')]]], 401);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, UnifyPhone $unifyPhone): JsonResponse
    {
        $user = User::query()
            ->where('phone',$unifyPhone->handle($request->phone))
            ->where('active',1)
            ->first();
        $attempts = $this->getSmsAttempts();

        if (!$user) {
            return response()->json(['errors' => ['phone' => [trans('auth.wrong_phone')]]], 401);
        } else if (time() - $user->updated_at->timestamp < (60 * 60 * 24)) {
            return response()->json(['message' => trans('auth.the_minute_has_not_expired')], 403);
        } else if ($attempts >= 3) {
            return response()->json(['message' => trans('auth.too_many_tries_delay')], 403);
        } else if (RateLimiter::tooManyAttempts('register:'.request('email'), $perMinute = 5)) {
            return response()->json(['message' => trans('auth.too_many_tries')], 425);
        } else {
            $this->setSmsAttempts($attempts + 1);
            $password = Str::random(5);
            $user->update(['password' => bcrypt($password)]);
            event(new ChangePasswordEvent($user, $password));
            return response()->json(['message' => trans('auth.new_password_has_been_sent_to_your_phone')],200);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('home'));
    }

    private function setSmsAttempts($attempts): void
    {
        Cookie::queue('sms-attempts', $attempts, (60 * 60 * 24));
    }

    private function getSmsAttempts(): int
    {
        return (int)Cookie::get('sms-attempts');
    }

    private function checkAttempts(User $user)
    {

    }
}
