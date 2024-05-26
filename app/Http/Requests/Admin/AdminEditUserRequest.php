<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use function request;

class AdminEditUserRequest extends FormRequest
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
        $rules = [
            'name' => 'nullable|max:255',
            'family' => 'nullable|max:255',
            'born' => $this->validationBorn,
            'phone' => $this->validationPhone,
            'email' => 'nullable|email|unique:users,email',
            'info_about' => $this->validationText,
            'mail_notice' => 'integer|min:0|max:1',
            'active' => 'integer|min:0|max:1',
            'admin' => 'integer|min:0|max:1'
        ];

        if (request()->has('id')) {
            $rules['id'] = $this->validationUserId;
            $rules['email'] .= ','.request('id');
            if (request('password')) $rules['password'] = $this->validationPassword;
        } else {
            $rules['password'] = $this->validationPassword;
        }

        return $rules;
    }
}
