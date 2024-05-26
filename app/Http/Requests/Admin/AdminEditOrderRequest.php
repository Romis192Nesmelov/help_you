<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\HelperTrait;
use Illuminate\Foundation\Http\FormRequest;
use function request;

class AdminEditOrderRequest extends FormRequest
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
            'user_id' => $this->validationUserId,
            'order_type_id' => 'required|exists:order_types,id',
            'subtype_id' => 'nullable|exists:subtypes,id',
            'name' => $this->validationName,
            'need_performers' => 'required|min:1|max:20',
            'address' => $this->validationString,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description_short' => 'nullable|string|max:200',
            'description_full' => 'nullable|string|max:1000',
            'status' => 'integer|min:0|max:3',
        ];

        if (request('id')) $rules['id'] = 'required|exists:orders,id';
        if (request('status') && request('status') == 1) $rules['performer_id'] = $this->validationUserId;
        if (request('performer_id')) $rules['status'] = 'required|integer|min:1|max:1';

        for ($i=1;$i<=3;$i++) {
            $fileName = 'photo'.$i;
            if (request()->hasFile($fileName)) {
                $rules[$fileName] = 'required|'.$this->validationJpgAndPng;
            }
        }
        return $rules;
    }
}
