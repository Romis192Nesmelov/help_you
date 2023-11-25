<?php

namespace Database\Seeders;

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
        $me = User::where('phone','+7(926)247-77-25')->select('id')->first();
        Order::factory(100)->create();
        $order = Order::create([
            'user_id' => $me->id,
            'order_type_id' => 1,
            'city_id' => 1,
            'subtype_id' => rand(1,4),
            'need_performers' => 10,
            'address' => 'Криворожская улица, 17, подъезд 2, этаж 3, кв. 32',
            'latitude' => 55.667594,
            'longitude' => 37.612245,
            'description_short' => 'Чета с кем-то где-то что-то и когда-то надо срочно сделать, а то край!',
            'approved' => 1,
            'status' => 1
        ]);

        $performers = [
            User::where('phone','+7(958)815-85-65')->select('id')->first(),
            User::where('phone','+7(925)521-37-45')->select('id')->first(),
            User::where('phone','+7(926)206-39-77')->select('id')->first()
        ];

        foreach ($performers as $performer) {
            OrderUser::create([
                'order_id' => $order->id,
                'user_id' => $performer->id
            ]);
        }
    }
}
