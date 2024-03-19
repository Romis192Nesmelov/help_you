<?php

namespace Database\Seeders;

use App\Models\InformingOrder;
use App\Models\Order;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\OrderUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $im = User::where('phone','+7(926)247-77-25')->select('id')->first();
        $anotherUser = User::where('phone','+7(958)815-85-65')->select('id')->first();

        $order = Order::create([
            'user_id' => $im->id,
            'order_type_id' => 1,
            'city_id' => 1,
            'subtype_id' => rand(1,4),
            'name' => 'Помощь на дому',
            'need_performers' => 1,
            'address' => 'Чечёрский проезд д.102 кв.97',
            'latitude' => 55.529496,
            'longitude' => 37.517940,
            'description_short' => 'Нужно прибраться в квартире и вынести мусор.',
            'status' => 1
        ]);

        OrderUser::create([
            'order_id' => $order->id,
            'user_id' => $anotherUser->id
        ]);

//        InformingOrder::create([
//            'message' => trans('content.to_over_order'),
//            'order_id' => $order->id
//        ]);
    }
}
