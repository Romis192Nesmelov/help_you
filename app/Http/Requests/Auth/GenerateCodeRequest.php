<?php

namespace App\Http\Requests\Auth;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
//use Illuminate\Validation\Rules\Password;

class GenerateCodeRequest extends FormRequest
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
        return [
            'phone' => 'required|'.$this->validationPhone,
//            'password' => ['required','confirmed',Password::defaults()],
            'password' => $this->validationPassword.'|confirmed',
            'i_agree' => 'accepted'
        ];
    }
}
