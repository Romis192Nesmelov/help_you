<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderImage;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::select('id')->get();
        foreach ($orders as $order) {
            for ($i=1;$i<=3;$i++) {
                OrderImage::create([
                    'image' => 'images/orders_images/random_order_image'.$i.'.jpg',
                    'order_id' => $order->id
                ]);
            }
        }
    }
}
