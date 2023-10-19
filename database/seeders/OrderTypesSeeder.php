<?php

namespace Database\Seeders;

use App\Models\OrderType;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Бытовая помощь', 'sub' => [
                ['id' => 1, 'name' => 'Перемещение крупногабаритных вещей'],
                ['id' => 2, 'name' => 'Вынос мусора'],
                ['id' => 3, 'name' => 'Помощь по ремонту'],
                ['id' => 4, 'name' => 'Выгул собак'],
            ]],
            ['name' => 'Передача ненужных вещей'],
            ['name' => 'Уход за захоронениями']
        ];

        foreach ($data as $item) {
            $subTypes = null;
            $item['active'] = 1;
            if (isset($item['sub'])) {
                $subTypes = $item['sub'];
                unset($item['sub']);
            }
            $orderType = OrderType::create($item);
            if ($subTypes) {
                $orderType->subtypes = $subTypes;
                $orderType->save();
            }
        }
    }
}
