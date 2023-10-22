<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\DeleteResponseRequest;
use App\Http\Requests\Order\NextStepRequest;
use App\Http\Requests\Order\OrderResponseRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\OrderUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderController extends BaseController
{
    public function orders(): View
    {
        $this->getItems('order_types', new OrderType());
        return $this->showView('orders');
    }

    public function getOrders(): JsonResponse
    {
        return response()->json(
            Order::query()
            ->default()
            ->filtered()
            ->with('orderType')
            ->with('user')
            ->with('performers')
            ->get(),
            200
        );
    }

    public function orderResponse(OrderResponseRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields['user_id'] = Auth::id();
        OrderUser::create($fields);
        return response()->json([],200);
    }

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

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteOrder(Request $request): JsonResponse
    {
        return $this->deleteSomething($request, new Order());
    }

    public function deleteResponse(DeleteResponseRequest $request): JsonResponse
    {
        $orderUser = OrderUser::where('order_id',$request->id)->where('user_id',Auth::id())->first();
        $orderUser->delete();
        return response()->json(['success' => true],200);
    }
}
