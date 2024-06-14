<?php

namespace App\Http\Requests\Tickets;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class NewAnswerRequest extends FormRequest
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
            'image' => $this->validationJpgAndPngSmall,
            'text' => $this->validationText,
            'ticket_id' => 'required|exists:tickets,id',
        ];
    }
}
