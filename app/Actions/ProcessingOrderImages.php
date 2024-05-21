<?php

namespace App\Actions;

use App\Http\Controllers\HelperTrait;
use App\Models\OrderImage;
use Illuminate\Http\Request;

class ProcessingOrderImages
{
    use HelperTrait;

    public function handle(Request $request, ProcessingImage $processingImage, int $orderId): void
    {
        for ($i=1;$i<=3;$i++) {
            $fieldName = 'photo'.$i;
            $imageFields = $processingImage->handle($request, [], $fieldName, 'images/orders_images/', 'order'.$orderId.'_'.$i);
            if (count($imageFields)) {
                $orderImage = OrderImage::where('position',$i)->where('order_id',$orderId)->first();
                if ($orderImage) {
                    $orderImage->image = $imageFields[$fieldName];
                    $orderImage->save();
                } else {
                    OrderImage::create([
                        'position' => $i,
                        'image' => $imageFields[$fieldName],
                        'order_id' => $orderId
                    ]);
                }
            }
        }
    }
}
