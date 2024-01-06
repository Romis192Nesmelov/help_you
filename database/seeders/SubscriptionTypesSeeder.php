<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\ReadOrder;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ids = [2,3,4,5];
        foreach ($ids as $id) {
            $usedIds = [];
            $subscript = 0;
            while ($subscript < 7) {
                $userId = User::all()->random()->id;
                if (!in_array($userId,$ids) && !in_array($userId,$usedIds)) {
                    $usedIds[] = $userId;
                    $subscription = Subscription::create([
                        'subscriber_id' => $id,
                        'user_id' => $userId,
                    ]);
                    $userLastOrders = Order::where('user_id',$userId)->orderByDesc('created_at')->limit(5)->get();
                    foreach ($userLastOrders as $userLastOrder) {
                        ReadOrder::create([
                            'subscription_id' => $subscription->id,
                            'order_id' => $userLastOrder->id,
                        ]);
                    }
                    $subscript++;
                }
            }
        }
    }
}
