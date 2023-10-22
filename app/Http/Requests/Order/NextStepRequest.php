<?php

namespace App\Http\Requests\Order;

use App\Models\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;


class NextStepRequest extends FormRequest
{
    public $stepsRules = [
        ['order_type_id' => 'required|exists:order_types,id', 'subtypes' => 'nullable|array'],
        ['need_performers' => 'required|integer|min:1|max:20'],
        ['address' => 'required|string|min:5|max:200', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric'],
        ['description' => 'nullable|string|min:5|max:3000']
    ];

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
        $step = Session::has('steps') ? count(Session::get('steps')) : 0;
        return $this->stepsRules[$step];
    }
}
