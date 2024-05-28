<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditTicketRequest extends FormRequest
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
            'subject' => 'required|min:3|max:255',
            'text' => $this->validationText,
            'user_id' => $this->validationUserId,
            'status' => 'integer|min:0|max:1',
            'read_admin' => 'integer|min:0|max:1'
        ];
        if (request()->has('id')) $rules['id'] = 'required|exists:tickets,id';
        if (request()->hasFile('image')) $rules['image'] = $this->validationJpgAndPngSmall;

        return $rules;
    }
}
