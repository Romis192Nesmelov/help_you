<?php

namespace App\Http\Controllers;
use App\Events\NotificationEvent;
use App\Events\OrderEvent;
use App\Http\Requests\Order\EditOrderRequest;
use App\Http\Requests\Order\RemovePerformerRequest;
use App\Http\Requests\Order\SetRatingRequest;
use App\Http\Resources\Orders\OrdersResource;
use App\Models\Message;
use App\Models\Rating;
use App\Models\ReadPerformer;
use App\Models\ReadRemovedPerformer;
use App\Models\ReadStatusOrder;
use App\Http\Requests\Order\DelOrderImageRequest;
use App\Http\Requests\Order\NextStepRequest;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\PrevStepRequest;
use App\Http\Requests\Order\ReadOrderRequest;
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
        $this->data['order_types'] = OrderType::where('active',1)->with('subtypesActive')->get();
        $this->data['session_key'] = 'steps';
        return $this->showView('edit_order');
    }

    public function orders(): View
    {
        $this->data['order_types'] = OrderType::where('active',1)->with('subtypesActive')->get();
        return $this->showView('orders');
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editOrder(EditOrderRequest $request): View
    {
        if ($request->has('id')) {
            $this->data['order'] = Order::with('images')->find($request->id);
            $this->authorize('owner', $this->data['order']);
        }
        $this->data['session_key'] = $this->getSessionKey($request);
        $this->data['order_types'] = OrderType::where('active',1)->with('subtypesActive')->get();
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

    public function getOrdersNews(): JsonResponse
    {
        return response()->json([
            'news_subscriptions' => ReadOrder::query()
                ->whereIn('subscription_id',Subscription::query()->default()->pluck('id')->toArray())
                ->where('read',null)
                ->with('order.user')
                ->get(),
            'news_performers' => ReadPerformer::query()
                ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
                ->where('read',null)
                ->with('order')
                ->with('user')
                ->get(),
            'news_removed_performers' => ReadRemovedPerformer::query()
                ->where('user_id', Auth::id())
                ->where('read', null)
                ->with('order')
                ->get(),
            'news_status_orders' => ReadStatusOrder::query()
                ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
                ->where('read',null)
                ->get()
        ]);
    }

    public function getOrders(): JsonResponse
    {
        return response()->json(OrdersResource::make([
            'orders' => Order::query()
                ->default()
                ->filtered()
                ->searched()
                ->with(['orderType','subType','images','user.ratings','performers'])
                ->get(),
            'subscriptions' => Subscription::query()
                ->with('orders')
                ->default()
                ->get()
        ])->resolve(), 200);
    }

    public function getPreview(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->where('user_id',Auth::id())
                ->where('status',2) /*TODO: default: 3 (on moderation) */
                ->filtered()
                ->with(['orderType','subType','images','user','performers'])
                ->orderByDesc('created_at')
                ->limit(1)
                ->get(),
            'subscriptions' => []
        ],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeOrderPerformer(RemovePerformerRequest $request): JsonResponse
    {
        $order = Order::find($request->order_id);
        $performer = User::find($request->user_id);
        $this->authorize('owner', $order);
        if (!$order->status) return response()->json([],403);

        OrderUser::where('order_id',$request->order_id)->where('user_id',$request->user_id)->delete();
        if (!$order->performers->count()) {
            $order->status = 2;
            $order->save();
        }

        ReadRemovedPerformer::create([
            'order_id' => $request->order_id,
            'user_id' => $request->user_id,
        ]);

        broadcast(new NotificationEvent('remove_performer', $order, $request->user_id));
        $this->mailNotice($order, $performer, 'remove_performer_notice');

        return response()->json(['message' => trans('content.the_performer_is_removed'), 'performers_count' => $order->performers->count()],200);
    }

    public function orderResponse(OrderRequest $request): JsonResponse
    {
        $fields['order_id'] = $request->id;
        $fields['user_id'] = Auth::id();
        $order = Order::find($request->id);
        if ($order->user_id == Auth::id()) return response()->json([],403);
        else {
            OrderUser::create($fields);
            ReadPerformer::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);
            ReadStatusOrder::create([
                'order_id' => $order->id,
                'status' => 1,
            ]);
            ReadOrder::where('order_id',$order->id)->delete();

            $order->status = 1;
            $order->save();
            $order->refresh();

            broadcast(new NotificationEvent('new_performer', $order, $order->user_id));
            $this->mailNotice($order, $order->userCredentials, 'new_performer_notice');
            if (!$order->messages->count()) $this->chatMessage($order, trans('content.new_chat_message'));

            broadcast(new NotificationEvent('new_order_status', $order, $order->user_id));
            $this->mailNotice($order, $order->userCredentials, 'new_order_status_notice');

            broadcast(new OrderEvent('remove_order', $order));

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
                'status' => 2 /*TODO: default: 3 (on moderation) */
            ];

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
                $this->newOrderInSubscription($order);
            }

            for ($i=1;$i<=3;$i++) {
                $fieldName = 'photo'.$i;
                $imageFields = $this->processingImage($request, [], $fieldName, 'images/orders_images/', 'order'.$order->id.'_'.$i);
                if (count($imageFields)) {
                    $orderImage = OrderImage::where('position',$i)->where('order_id',$order->id)->first();
                    if ($orderImage) {
                        $orderImage->image = $imageFields[$fieldName];
                        $orderImage->save();
                    } else {
                        OrderImage::create([
                            'position' => $i,
                            'image' => $imageFields[$fieldName],
                            'order_id' => $order->id
                        ]);
                    }
                }
            }
            return response()->json([
                'order' => $order,
                'image_fields' => $imageFields ?? 'none'
            ],200);
        } else {
            return response()->json([],200);
        }
    }

    public function prevStep(PrevStepRequest $request): JsonResponse
    {
        $sessionKey = $this->getSessionKey($request);
        $steps = Session::get($sessionKey);
        array_pop($steps);
        Session::put($sessionKey,$steps);
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

            $this->removeOrderUnreadMessages($request->id);
            broadcast(new OrderEvent('remove_order', $order));

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

        $this->removeOrderUnreadMessages($request->id);
        broadcast(new OrderEvent('remove_order', $order));

        return response()->json([],200);
    }

    public function setRating(SetRatingRequest $request): JsonResponse
    {
        $order = Order::find($request->order_id);
        foreach ($order->performers as $performer) {
            $rating = Rating::where('order_id',$order->id)->where('user_id',$performer->id)->first();
            if ($rating) {
                $rating->value = $request->rating;
                $rating->save();
            } else {
                Rating::create([
                    'value' => $request->rating,
                    'order_id' => $order->id,
                    'user_id' => $performer->id
                ]);
            }
        }
        return response()->json([],200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resumeOrder(OrderRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        $this->authorize('owner', $order);
        $order->status = 3;
        $order->save();

        OrderUser::where('order_id',$request->id)->delete();
        $this->newOrderInSubscription($order);
        return response()->json([],200);
    }

    public function deleteResponse(OrderRequest $request): JsonResponse
    {
        $orderUser = OrderUser::where('order_id',$request->id)->where('user_id',Auth::id())->first();
        $orderUser->delete();
        return response()->json([],200);
    }

    private function newOrderInSubscription(Order $order): void
    {
        $subscriptions = Subscription::where('user_id',Auth::id())->get();
        foreach ($subscriptions as $subscription) {
            ReadOrder::create([
                'subscription_id' => $subscription->id,
                'order_id' => $order->id,
            ]);

            /*TODO: enable after approving */
//            broadcast(new NotificationEvent('new_order_in_subscription', $order, $subscription->subscriber_id));
//            $this->mailNotice($order, $subscription->subscriber, 'new_order_in_subscription');
        }
    }

    private function removeOrderUnreadMessages($orderId): void
    {
        Message::where('order_id',$orderId)->delete();
        OrderUser::where('order_id',$orderId)->delete();
        ReadOrder::where('order_id',$orderId)->delete();
        ReadPerformer::where('order_id',$orderId)->delete();
        ReadRemovedPerformer::where('order_id',$orderId)->delete();
        ReadRemovedPerformer::where('order_id',$orderId)->delete();
    }
}
