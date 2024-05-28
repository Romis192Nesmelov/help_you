<?php

namespace App\Http\Controllers\Admin;
use App\Actions\DeleteFile;
use App\Actions\DeleteOrder;
use App\Actions\OrderResponse;
use App\Actions\ProcessingImage;
use App\Actions\ProcessingOrderImages;
use App\Actions\RemoveOrderImage;
use App\Actions\RemoveOrderUnreadMessages;
use App\Events\Admin\AdminOrderEvent;
use App\Events\NotificationEvent;
use App\Events\OrderEvent;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Admin\AdminEditOrderRequest;
use App\Http\Requests\Order\DelOrderImageRequest;
use App\Http\Requests\Order\OrderRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminOrdersController extends AdminBaseController
{
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function orders(Order $order, User $user, $slug=null): View
    {
        $this->data['users'] = User::query()->default()->get();
        $this->data['types'] = OrderType::with(['subtypes'])->get();
        return $this->getSomething($order, $slug, ['performers','images'], $user);
    }

    public function getOrders(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->withUserId()
                ->filtered()
                ->with(['user.ratings','orderType'])
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'users' => User::query()->default()->get(),
            'types' => OrderType::query()->select(['id','name'])->get(),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editOrder(
        AdminEditOrderRequest $request,
        ProcessingImage $processingImage,
        ProcessingOrderImages $actionOrderImages,
        RemoveOrderUnreadMessages $removeOrderUnreadMessages,
        OrderResponse $orderResponse,
    ): JsonResponse
    {
        $fields = $request->validated();
        $lastStatus = 0;
        if ($request->has('id')) {
            $order = Order::query()
                ->where('id',$request->id)
                ->with(['user.ratings','performers','images','adminNotice','userCredentials'])
                ->first();

            $lastStatus = $order->status;

            $order->update($fields);
            $order->refresh();
            if ($order->wasChanged()) broadcast(new AdminOrderEvent('change_item', $order));

            if ($order->adminNotice && $order->adminNotice->read != 1) {
                $order->adminNotice->read = 1;
                $order->adminNotice->save();
            }
        } else {
            $order = Order::query()->create($fields);
            $order->load(['user.ratings','performers','images']);
            /** @var ORDER $order */
            broadcast(new AdminOrderEvent('new_item', $order));
        }
        $actionOrderImages->handle($request, $processingImage, $order->id);

        if ($order->status != $lastStatus) {
            $this->mailOrderNotice($order, $order->userCredentials, 'new_order_status_notice');
            broadcast(new NotificationEvent('new_order_status', $order, $order->user_id));
            broadcast(new OrderEvent('new_order_status', $order));

            if (!$order->status) {
                $removeOrderUnreadMessages->handle($order->id);
                broadcast(new OrderEvent('remove_order', $order));
            } elseif ($order->status == 1) {
                OrderUser::query()->create(['order_id' => $order->id, 'user_id' => $request->performer_id]);

                $removeOrderUnreadMessages->handle($order->id);
                $orderResponse->handle($order);
                broadcast(new NotificationEvent('new_performer', $order, $order->user_id));

                $this->mailOrderNotice($order, $order->userCredentials, 'new_performer_notice');
                if (!$order->messages->count()) $this->chatMessage($order, trans('content.new_chat_message'));
            } elseif ($order->status == 2) {
                $subscriptions = Subscription::query()->where('user_id',$order->user_id)->with('unreadOrders')->get();
                foreach ($subscriptions as $subscription) {
                    ReadOrder::create([
                        'subscription_id' => $subscription->id,
                        'order_id' => $order->id,
                    ]);
                    broadcast(new NotificationEvent('new_order_in_subscription', $order, $subscription->subscriber_id));
                    $this->mailOrderNotice($order, $subscription->subscriber, 'new_order_in_subscription');
                }
                OrderUser::query()->where('order_id',$order->id)->delete();
            } elseif ($order->status == 3) {
                $removeOrderUnreadMessages->handle($order->id);
                OrderUser::query()->where('order_id',$order->id)->delete();
            }
        }

//        $this->saveCompleteMessage();
//        return redirect()->back();
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function deleteOrderImage(
        DelOrderImageRequest $request,
        RemoveOrderImage $removeOrderImage,
        DeleteFile $deleteFile
    ): JsonResponse
    {
        $order = Order::query()->where('id',$request->id)->with(['user.ratings','performers','images'])->first();
        $removeOrderImage->handle($order->id, $deleteFile, $request->pos);
        broadcast(new AdminOrderEvent('change_item', $order));
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteOrder(
        OrderRequest $request,
        DeleteOrder $deleteOrder,
        DeleteFile $deleteFile,
    ): JsonResponse
    {
        return $deleteOrder->handle(Order::where('id',$request->id)->with(['images','messages'])->first(), $deleteFile);
    }
}
