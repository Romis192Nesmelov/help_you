<?php

namespace App\Actions;

use App\Http\Controllers\HelperTrait;
use App\Models\Order;
use App\Models\OrderImage;

class RemoveOrderImage
{
    use HelperTrait;

    public function handle(int $orderId, DeleteFile $deleteFile, int $pos): void
    {
        $fileName = 'images/orders_images/order'.$orderId.'_'.$pos.'.jpg';
        OrderImage::where('image',$fileName)->delete();
        $deleteFile->handle($fileName);
    }
}
