<?php

namespace Database\Seeders;

use App\Models\Order;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'order_type_id' => 1,
            'city_id' => 1,
            'subtypes' => [1,2,3,4],
            'need_performers' => 1,
            'address' => 'Криворожская улица, 17, подъезд 2, этаж 3, кв. 32',
            'latitude' => 55.667594,
            'longitude' => 37.612245,
            'description' => 'Чета с кем-то где-то что-то и когда-то надо срочно сделать, а то край!',
            'approved' => 1,
            'status' => 1
        ]);
    }
}
