<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\NextStepRequest;
use App\Models\OrderType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderController extends BaseController
{
    public function newOrder(): View
    {
        Session::forget('steps');
//        if (Session::has('steps') && count(Session::get('steps')) == 4) Session::forget('steps');
        $this->getItems('order_types', new OrderType());
        return $this->showView('new_order');
    }

    public function nextStep(NextStepRequest $request): JsonResponse
    {
        $fields = $request->validated();
        if (Session::has('steps')) {
            $steps = Session::get('steps');
            $steps[] = $fields;
        } else {
            $steps = [$fields];
        }
        Session::put('steps',$steps);
        return response()->json([],200);
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
