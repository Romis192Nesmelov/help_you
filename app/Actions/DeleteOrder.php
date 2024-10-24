<?php

namespace App\Actions;

use App\Events\Admin\AdminOrderEvent;
use App\Events\OrderEvent;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class DeleteOrder
{
    public function handle(
        Order $order,
        DeleteFile $deleteFile,
    ): JsonResponse
    {
        foreach ($order->images as $image) {
            $deleteFile->handle($image->image);
        }

        foreach ($order->messages as $message) {
            if ($message->image) $deleteFile->handle($message->image);
        }

        broadcast(new OrderEvent('remove_order', $order));
        broadcast(new AdminOrderEvent('del_item', $order));

        $order->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
