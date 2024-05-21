<?php

namespace App\Actions;

use App\Events\Admin\AdminOrderEvent;
use App\Http\Controllers\HelperTrait;
use App\Models\Order;
use App\Models\OrderImage;
use Illuminate\Http\JsonResponse;

class RemoveOrderImage
{
    use HelperTrait;

    public function handle(Order $order, DeleteFile $deleteFile, int $pos): JsonResponse
    {
        $fileName = 'images/orders_images/order'.$order->id.'_'.$pos.'.jpg';
        OrderImage::where('image',$fileName)->delete();
        $deleteFile->handle($fileName);
        broadcast(new AdminOrderEvent('change_item', $order));
        return response()->json([],200);
    }
}
