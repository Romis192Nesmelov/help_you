<?php

namespace Database\Seeders;

use App\Models\AdminNotice;
use App\Models\Order;
use Illuminate\Database\Seeder;

class AdminNoticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newOrders = Order::where('status',3)->select('id')->get();
        foreach ($newOrders as $order) {
            AdminNotice::create(['order_id' => $order->id]);
        }
    }
}
