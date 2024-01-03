<?php

namespace App\Http\Requests\Account;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditAccountRequest extends FormRequest
{
    use HelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validationArr = [
            'avatar_size' => 'nullable|integer',
            'avatar_position_x' => 'nullable',
            'avatar_position_y' => 'nullable',
            'name' => $this->validationString,
            'family' => $this->validationString,
            'born' => $this->validationBorn,
            'email' => 'nullable|email|unique:users,email,'.Auth::id(),
            'info_about' => $this->validationText,
            'mail_notice' => 'nullable'
        ];

        if ($this->hasFile('avatar')) $validationArr['avatar'] = $this->validationJpgAndPng;
        return $validationArr;
    }
}
