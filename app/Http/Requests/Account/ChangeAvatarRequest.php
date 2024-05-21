<?php

namespace App\Http\Requests\Account;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use function request;

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
        $rules = [
            'avatar' => 'required|'.$this->validationJpgAndPngSmall,
            'avatar_size' => 'nullable|integer',
            'avatar_position_x' => 'nullable',
            'avatar_position_y' => 'nullable',
        ];
        if (request('id')) $rules['id'] = $this->validationUserId;

        return $rules;
    }
}
