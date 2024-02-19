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
            'name' => 'Тестовая заяка',
            'need_performers' => 10,
            'address' => 'Криворожская улица, 17, подъезд 2, этаж 3, кв. 32',
            'latitude' => 55.667594,
            'longitude' => 37.612245,
            'description_short' => 'Чета с кем-то где-то что-то и когда-то надо срочно сделать, а то край!',
            'status' => 2
        ]);

//        OrderUser::create([
//            'order_id' => $order->id,
//            'user_id' => $anotherUser->id
//        ]);

//        InformingOrder::create([
//            'message' => trans('content.to_over_order'),
//            'order_id' => $order->id
//        ]);
    }
}
