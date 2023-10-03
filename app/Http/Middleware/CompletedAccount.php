<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompletedAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guest()) {
            $errors = [];
            foreach (['name','family'] as $item) {
                if (!Auth::user()->$item) $errors[$item] = trans('validation.field_must_be_filled_in');
            }
            if (count($errors)) return redirect(route('account.change'))
                ->with('message', trans('content.you_must_fill_required_fields'))
                ->withErrors($errors);
        }
        return $next($request);
    }
}
