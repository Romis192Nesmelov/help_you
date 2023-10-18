<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\NextStepRequest;
use App\Models\Order;
use App\Models\OrderType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderController extends BaseController
{
    public function newOrder(): View
    {
        $this->getItems('order_types', new OrderType());
        return $this->showView('new_order');
    }

    public function nextStep(NextStepRequest $request): JsonResponse
    {
        $fields = $request->validated();
        if (Session::has('steps')) {
            $steps = Session::get('steps');
            $steps[] = $fields;
        } else $steps = [$fields];

        if (count($steps) == 4) {
            $steps = Session::get('steps');
            Session::forget('steps');
            $fields = [
                'city_id' => 1,
                'user_id' => Auth::id(),
                'active' => 1,
                'approved' => 0
            ];
            foreach ($steps as $k => $step) {
                if (isset($step['subtypes'])) {
                    $subtypes = [];
                    foreach ($step['subtypes'] as $subtype) {
                        $subtypes[] = (int)$subtype;
                    }
                    $step['subtypes'] = $subtypes;
                }
                $fields = array_merge($fields,$step);
            }
            $order = Order::create($fields);
            return response()->json(['order' => $order],200);
        } else {
            Session::put('steps',$steps);
            return response()->json([],200);
        }
    }

    public function prevStep(): JsonResponse
    {
        $steps = Session::get('steps');
        if (count($steps) == 1) Session::forget('steps');
        else {
            array_pop($steps);
            Session::put('steps',$steps);
        }
        return response()->json([],200);
    }
}
