<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditPartnerRequest extends FormRequest
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
            'about' => $this->validationText,
            'info' => $this->validationLongText
        ];

        if (request()->has('id')) $rules['id'] = 'required|exists:orders,id';
        if (request()->hasFile('logo')) $rules['logo'] = $this->validationJpgAndPngAndSvgSmall;

        return $rules;
    }
}
