<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\DeleteResponseRequest;
use App\Http\Requests\Order\NextStepRequest;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\OrderResponseRequest;
use App\Http\Requests\Order\ReadOrderRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderController extends BaseController
{
    public function newOrder(): View
    {
        $this->getItems('order_types', new OrderType());
        return $this->showView('edit_order');
    }

    public function orders(Request $request): View
    {
        $this->getItems('order_types', new OrderType());
        $this->data['order_preview'] = $request->has('preview') && $request->preview;
        return $this->showView('orders');
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editOrder(OrderRequest $request): View
    {
//        Session::forget('steps');
        $this->data['order'] = Order::find($request->id);
        $this->authorize('owner', $this->data['order']);
        $this->getItems('order_types', new OrderType());
        return $this->showView('edit_order');
    }

    public function readOrder(ReadOrderRequest $request): JsonResponse
    {
        $readOrder = ReadOrder::where('order_id',$request->order_id)->first();
        $this->authorize('subscriber', $readOrder->subscription);
        $readOrder->read = true;
        $readOrder->save();
        return response()->json([],200);
    }

    public function getOrders(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->default()
                ->filtered()
                ->with('orderType')
                ->with('user')
                ->with('performers')
                ->get(),
            'subscriptions' => Subscription::query()
                ->with('orders')
                ->default()
                ->get()
        ],
            200
        );
    }

    public function getPreview(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->where('user_id',Auth::id())
                ->where('active',1)
                ->where('approved',0)
                ->filtered()
                ->with('orderType')
                ->with('user')
                ->with('performers')
                ->latest('created_at')
                ->limit(1)
                ->get(),
            'subscriptions' => []
        ],
            200
        );
    }

    public function orderResponse(OrderResponseRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields['user_id'] = Auth::id();
        $order = Order::find($fields['order_id']);
        if ($order->user_id == Auth::id()) return response()->json([],403);
        else {
            OrderUser::create($fields);
            return response()->json([],200);
        }
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

            if ($request->has('id')) {
                $order = Order::find($request->id);
                $this->authorize('owner', $order);
                $order->update($fields);
            } else $order = Order::create($fields);

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
        return $this->deleteSomething($request, new Order(), 'owner');
    }

    public function deleteResponse(DeleteResponseRequest $request): JsonResponse
    {
        $orderUser = OrderUser::where('order_id',$request->id)->where('user_id',Auth::id())->first();
        $orderUser->delete();
        return response()->json([],200);
    }
}
