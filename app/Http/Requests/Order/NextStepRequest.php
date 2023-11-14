<?php

namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;


class NextStepRequest extends FormRequest
{
    public array $stepsRules = [
        ['order_type_id' => 'required|exists:order_types,id', 'subtype_id' => 'nullable|exists:subtypes,id'],
        ['need_performers' => 'required|integer|min:1|max:20'],
        ['address' => 'required|string|min:5|max:200', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric'],
        ['id' => 'nullable|exists:orders,id', 'description_short' => 'nullable|string|min:5|max:200', 'description_full' => 'nullable|string|min:5|max:1000']
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
        $rules = $this->stepsRules[$step];
        if ($step == 3) {
            for ($i=1;$i<=3;$i++) {
                $fileName = 'photo'.$i;
                if (request()->hasFile($fileName)) {
                    $rules[$fileName] = 'image|max:700';
                }
            }
        }
        return $rules;
    }
}
