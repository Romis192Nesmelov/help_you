<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editAccount(Request $request): RedirectResponse
    {
        $fields = $this->validate($request, [
            'avatar' => $this->validationJpgAndPng,
            'name' => $this->validationString,
            'family' => $this->validationString,
            'born' => $this->validationBorn,
            'email' => 'required|email|unique:users,email,'.auth()->id(),
            'phone' => $this->validationPhone,
            'info_about' => $this->validationText
        ]);

        $bornDate = explode('-',$fields['born']);
        if (
            !$bornDate[1] ||
            !$bornDate[2] ||
            ((int)$bornDate[2] > cal_days_in_month(CAL_GREGORIAN, $bornDate[1], $bornDate[0])) ||
            ((int)$bornDate[0] > (int)date('Y') - 18) ||
            ((int)$bornDate[0] == (int)date('Y') - 18 && (int)$bornDate[1] < (int)date('m')) ||
            ((int)$bornDate[0] == (int)date('Y') - 18 && (int)$bornDate[1] == (int)date('m') && (int)$bornDate[2] < (int)date('d'))
        ) return redirect()->back()->withErrors(['born' => trans('validation.wrong_date')]);

        $fields = $this->processingImage($request, $fields,'avatar', 'images/avatars/', Auth::id());
        Auth::user()->update($fields);
        return redirect(route('account'))->with('message',trans('content.save_complete'));
    }
}
