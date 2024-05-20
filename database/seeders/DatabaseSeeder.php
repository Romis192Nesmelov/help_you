<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Action;
use App\Models\Order;
use Illuminate\Database\Seeder;
use App\Models\Partner;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(OrderTypesSeeder::class);
        Order::factory(50)->create();
        $this->call(OrdersSeeder::class);
//        $this->call(OrderImagesSeeder::class);
        $this->call(SubscriptionTypesSeeder::class);
        $this->call(MessageKeywordsSeeder::class);
        $this->call(PartnersSeeder::class);
        $this->call(ActionsSeeder::class);
        $this->call(ActionsUsersSeeder::class);
    }
}
