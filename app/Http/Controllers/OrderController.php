<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Order\DelOrderImageRequest;
use App\Http\Requests\Order\NextStepRequest;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\PrevStepRequest;
use App\Http\Requests\Order\ReadOrderRequest;
use App\Http\Requests\Order\UserAgeRequest;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderType;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderController extends BaseController
{
    use HelperTrait;

    public function newOrder(): View
    {
        $this->getItems('order_types', new OrderType());
        $this->data['session_key'] = 'steps';
        return $this->showView('edit_order');
    }

    public function orders(): View
    {
        $this->getItems('order_types', new OrderType());
        return $this->showView('orders');
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editOrder(OrderRequest $request): View
    {
        $this->data['order'] = Order::find($request->id);
        $this->authorize('owner', $this->data['order']);
        $this->getItems('order_types', new OrderType());
        $this->data['session_key'] = 'edit'.$this->data['order']->id.'_steps';
        return $this->showView('edit_order');
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function readOrder(ReadOrderRequest $request): JsonResponse
    {
        $readOrder = ReadOrder::where('order_id',$request->order_id)->first();
        $this->authorize('subscriber', $readOrder->subscription);
        $readOrder->read = true;
        $readOrder->save();
        return response()->json([],200);
    }

    public function getSubscriptionsNews(): JsonResponse
    {
        return response()->json([
            'subscriptions' => Subscription::query()
                ->with('unreadOrders.order.user')
                ->default()
                ->get()
        ]);
    }

    public function getOrders(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->default()
                ->filtered()
                ->with(['orderType','subType','images','user','performers'])
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
                ->where('status',2)
                ->where('approved',0)
                ->filtered()
                ->with(['orderType','subType','images','user','performers'])
                ->orderByDesc('created_at')
                ->limit(1)
                ->get(),
            'subscriptions' => []
        ],
            200
        );
    }

    public function getUserAge(UserAgeRequest $request): JsonResponse
    {
        $user = User::find($request->id);
        $age = $user->years();
        if ($age == 1) $word = 'год';
        elseif ($age > 1 && $age < 5) $word = 'года';
        elseif ($age >= 5 && $age < 21) $word = 'лет';
        else {
            $lastDigit = (int)substr($age, -1, 1);
            if ($lastDigit == 0) $word = 'лет';
            elseif ($lastDigit == 1) $word = 'год';
            elseif ($lastDigit > 1 && $lastDigit < 5) $word = 'года';
            else $word = 'лет';
        }
        return response()->json(['age' => $age.' '.$word]);
    }

    public function orderResponse(OrderRequest $request): JsonResponse
    {
        $fields['order_id'] = $request->id;
        $fields['user_id'] = Auth::id();
        $order = Order::find($request->id);
        if ($order->user_id == Auth::id()) return response()->json([],403);
        else {
            OrderUser::create($fields);
            $order = $order->refresh();

            $this->newChatMessage($order);

            $order->status = 1;
            $order->save();

//            if ($order->performers->count() >= $order->need_performers) {
//                $order->status = 1;
//                $order->save();
//            }
            return response()->json([],200);
        }
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function nextStep(NextStepRequest $request): JsonResponse
    {
        $sessionKey = $this->getSessionKey($request);
        $fields = $request->validated();
        if (Session::has($sessionKey)) {
            $steps = Session::get($sessionKey);
            if (count($steps) == 1) {
                for ($i=1;$i<=3;$i++) {
                    unset($fields['photo'.$i]);
                }
            }
            $steps[] = $fields;
        } else $steps = [$fields];

        Session::put($sessionKey,$steps);

        if (count($steps) == 4) {
            $steps = Session::get($sessionKey);
            Session::forget($sessionKey);

            $fields = [
                'city_id' => 1,
                'user_id' => Auth::id(),
                'status' => 2,
                'approved' => 1
            ];
            // Statuses: 2 – new; 1 – in progress; 0 – closed

            foreach ($steps as $step) {
                $fields = array_merge($fields,$step);
            }

            $orderType = OrderType::find($steps[0]['order_type_id']);
            if (!$orderType->subtypes->count()) unset($fields['subtype_id']);
            if ($request->has('id')) {
                $order = Order::find($request->id);

                $this->authorize('owner', $order);
                if (!$order->status) return response()->json([],403);

                $order->update($fields);

            } else {
                $order = Order::create($fields);
                $this->newOrderInSubscription($order->id);
            }

            for ($i=1;$i<=3;$i++) {
                $fieldName = 'photo'.$i;
                $imageFields = $this->processingImage($request, [], $fieldName, 'images/orders_images/', 'order'.$order->id.'_'.$i);
                if (count($imageFields)) {
                    OrderImage::create([
                        'position' => $i,
                        'image' => $imageFields[$fieldName],
                        'order_id' => $order->id
                    ]);
                }
            }
            return response()->json(['order' => $order],200);
        } else {
            return response()->json([],200);
        }
    }

    public function prevStep(PrevStepRequest $request): JsonResponse
    {
        $sessionKey = $this->getSessionKey($request);
        $steps = Session::get($sessionKey);
        if (count($steps) == 1) Session::forget($sessionKey);
        else {
            array_pop($steps);
            Session::put($sessionKey,$steps);
        }
        return response()->json([],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteOrder(OrderRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        $this->authorize('owner', $order);
        if ($order->status <= 1) return response()->json([],403);
        else {
            foreach ($order->images as $image) {
                $this->deleteFile($image->image);
            }
            $order->delete();
            return response()->json([],200);
        }
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteOrderImage(DelOrderImageRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        $this->authorize('owner', $order);
        $fileName = 'images/orders_images/order'.$order->id.'_'.$request->pos.'.jpg';
        $orderImage = OrderImage::where('image',$fileName)->first();
        $orderImage->delete();
        $this->deleteFile($fileName);
        return response()->json([],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function closeOrder(OrderRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        $this->authorize('owner', $order);
        $order->status = 0;
        $order->save();
        ReadOrder::where('order_id')->delete();
        return response()->json([],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resumeOrder(OrderRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        $this->authorize('owner', $order);
        $order->status = 2;
        $order->approved = 0;
        $order->save();

        OrderUser::where('order_id')->delete();
        $this->newOrderInSubscription($order->id);
        return response()->json([],200);
    }

    public function deleteResponse(OrderRequest $request): JsonResponse
    {
        $orderUser = OrderUser::where('order_id',$request->id)->where('user_id',Auth::id())->first();
        $orderUser->delete();
        return response()->json([],200);
    }

    private function newOrderInSubscription(int $orderId): void
    {
        $subscriptions = Subscription::where('user_id',Auth::id())->get();
        foreach ($subscriptions as $subscription) {
            ReadOrder::create([
                'subscription_id' => $subscription->id,
                'order_id' => $orderId,
            ]);
        }
    }
}
