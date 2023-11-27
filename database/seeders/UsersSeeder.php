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
        User::factory(10)->create();
        $data = [
            [
                'name' => 'Роман',
                'family' => 'Несмелов',
                'born' => '14-07-1976',
                'phone' => '+7(926)247-77-25',
                'email' => 'romis@nesmelov.com',
                'password' => bcrypt('apg192'),
                'code' => '99-99-99',
                'active' => 1
            ],
            [
                'name' => 'Romis',
                'family' => 'Nesmelov',
                'born' => '14-07-1976',
                'phone' => '+7(958)815-85-65',
                'email' => 'romis.nesmelov@gmail.com',
                'password' => bcrypt('apg192'),
                'code' => '99-99-99',
                'active' => 1
            ],
            [
                'name' => 'Unknown',
                'family' => 'Unknown',
                'born' => '14-07-1976',
                'phone' => '+7(925)521-37-45',
                'email' => 'unknown@mail.ru',
                'password' => bcrypt('123456'),
                'code' => '99-99-99',
                'active' => 1
            ],
            [
                'name' => 'Anonymous',
                'family' => 'Anonymous',
                'phone' => '+7(926)206-39-77',
                'email' => 'anonymous@mail.ru',
                'password' => bcrypt('123456'),
                'code' => '99-99-99',
                'active' => 1
            ]
        ];

        foreach ($data as $item) {
            User::create($item);
        }
    }
}
