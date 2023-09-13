<?php

namespace Database\Seeders;

use App\Models\User;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(50)->create();
        User::create([
            'name' => 'Роман',
            'family' => 'Несмелов',
            'born' => '1976-7-14',
            'phone' => '+7(926)247-77-25',
            'email' => 'romis@nesmelov.com',
            'password' => bcrypt('apg192'),
            'code' => '99-99-99',
            'active' => 1
        ]);
    }
}
