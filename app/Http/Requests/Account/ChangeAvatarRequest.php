<?php

namespace App\Http\Requests\Account;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangeAvatarRequest extends FormRequest
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
            'avatar' => 'required|'.$this->validationJpgAndPng,
            'avatar_size' => 'nullable|integer',
            'avatar_position_x' => 'nullable',
            'avatar_position_y' => 'nullable',
        ];
    }
}
