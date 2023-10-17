<?php

namespace App\Http\Requests\Order;

use App\Models\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;


class NextStepRequest extends FormRequest
{
    public $stepsRules = [
        'step1_1'   => ['order_type' => 'required|exists:order_types,id'],
        'step1_2'   => ['subtype' => 'required|array|exists:order_sub_types,id'],
        'step2'     => ['performers' => 'required|integer|min:1|max:20'],
        'step3'     => ['address' => 'required|string|min:5|max:200'],
        'step4'     => ['description' => 'nullable|string|min:5|max:3000'],
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
        if (!Session::has('steps')) {
            $orderType = OrderType::findOrFail($this->order_type);
            if (count($orderType->subTypesActive)) return array_merge($this->stepsRules['step1_1'], $this->stepsRules['step1_2']);
            else return $this->stepsRules['step1_1'];
        } else {
            return $this->stepsRules['step'.count(Session::get('steps'))+1];
        }
    }
}
