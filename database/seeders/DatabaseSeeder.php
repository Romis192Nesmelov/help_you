<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Answer;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

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
//        $this->call(OrdersSeeder::class);
        $this->call(AdminNoticesSeeder::class);
//        $this->call(OrderImagesSeeder::class);
        $this->call(SubscriptionTypesSeeder::class);
        $this->call(MessageKeywordsSeeder::class);
        $this->call(PartnersSeeder::class);
        $this->call(ActionsSeeder::class);
        $this->call(ActionsUsersSeeder::class);
        Ticket::factory(50)->create();
        Answer::factory(200)->create();
    }
}
