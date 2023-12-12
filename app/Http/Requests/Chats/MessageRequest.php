<?php

namespace App\Http\Requests\Chats;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id',
            'body' => 'nullable|min:1|max:255'
        ];
    }
}
