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
        $data = [
            [
                'name' => 'Администрация',
                'family' => 'Ресурса',
                'born' => '14-07-1976',
                'phone' => '+7(000)000-00-00',
                'email' => 'info@need-help.online',
                'password' => bcrypt('123456'),
                'code' => '99-99-99',
                'admin' => 1,
                'active' => 1
            ],
            [
                'name' => 'Роман',
                'family' => 'Несмелов',
                'born' => '14-07-1976',
                'phone' => '+7(926)247-77-25',
                'email' => 'romis@nesmelov.com',
                'password' => bcrypt('apg192'),
                'code' => '99-99-99',
                'admin' => 1,
                'active' => 1
            ],
//            [
//                'name' => 'Romis',
//                'family' => 'Nesmelov',
//                'born' => '14-07-1976',
//                'phone' => '+7(958)815-85-65',
//                'email' => 'romis.nesmelov@gmail.com',
//                'password' => bcrypt('apg192'),
//                'code' => '99-99-99',
//                'admin' => 1,
//                'active' => 1
//            ],
//            [
//                'name' => 'Unknown',
//                'family' => 'Unknown',
//                'born' => '14-07-1976',
//                'phone' => '+7(925)521-37-45',
//                'email' => 'unknown@mail.ru',
//                'password' => bcrypt('123456'),
//                'code' => '99-99-99',
//                'admin' => 1,
//                'active' => 1
//            ],
//            [
//                'name' => 'Anonymous',
//                'family' => 'Anonymous',
//                'phone' => '+7(926)206-39-77',
//                'born' => '14-07-1976',
//                'email' => 'anonymous@mail.ru',
//                'password' => bcrypt('123456'),
//                'code' => '99-99-99',
//                'admin' => 1,
//                'active' => 1
//            ]
        ];

        foreach ($data as $item) {
            User::create($item);
        }

//        for ($i=1;$i<=15;$i++) {
//            User::create([
//                'name' => 'User'.($i),
//                'family' => 'Test'.($i),
//                'phone' => '+7(926)111-11-'.($i < 10 ? '0'.$i : $i),
//                'born' => '01-01-1970',
//                'email' => 'user'.($i).'@mail.ru',
//                'password' => bcrypt('123456'),
//                'code' => '99-99-99',
//                'admin' => 1,
//                'active' => 1
//            ]);
//        }
//        User::factory(10)->create();
    }
}
