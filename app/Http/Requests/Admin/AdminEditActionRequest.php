<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditActionRequest extends FormRequest
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
            'name' => $this->validationName,
            'html' => $this->validationLongText,
            'start' => $this->validationDate,
            'end' => $this->validationDate,
            'rating' => 'required|min:1|max:2',
            'users_ids' => 'required|string',
            'partner_id' => 'required|integer|exists:partners,id'
        ];

        if (request()->has('id')) $rules['id'] = 'required|exists:actions,id';
        if (request()->has('user_ids')) {
            $rules['user_ids'] = 'required|array';
            $rules['user_ids.*'] = 'exists:users,id';
        }

        return $rules;
    }
}
