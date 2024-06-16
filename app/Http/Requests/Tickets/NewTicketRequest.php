<?php

namespace App\Http\Requests\Tickets;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class NewTicketRequest extends FormRequest
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
            'image' => 'nullable|'.$this->validationJpgAndPngSmall,
            'subject' => $this->validationString,
            'text' => 'required|min:1|max:3000'
        ];
    }
}
