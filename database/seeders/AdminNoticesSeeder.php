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
        $orders = Order::all();
        foreach ($orders as $order) {
            $fields = [
                'read' => $order->status == 0 || $order->status == 3 ? null : 1,
                'order_id' => $order->id
            ];
            AdminNotice::query()->create($fields);
        }
    }
}
