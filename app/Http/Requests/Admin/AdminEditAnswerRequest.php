<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditAnswerRequest extends FormRequest
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
            'text' => $this->validationText,
            'ticket_id' => 'required|integer|exists:tickets,id',
            'user_id' => $this->validationUserId,
            'read_admin' => 'integer|min:0|max:1',
            'read_owner' => 'integer|min:0|max:1',
        ];
        if (request()->has('id')) $rules['id'] = 'required|exists:answers,id';
        if (request()->hasFile('image')) $rules['image'] = $this->validationJpgAndPngSmall;

        return $rules;
    }
}
