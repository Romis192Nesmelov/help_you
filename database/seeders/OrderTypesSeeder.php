<?php

namespace Database\Seeders;

use App\Models\OrderType;
use App\Models\SubType;
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
                ['name' => 'Перемещение крупногабаритных вещей'],
                ['name' => 'Вынос мусора'],
                ['name' => 'Помощь по ремонту'],
                ['name' => 'Выгул собак'],
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
                foreach ($subTypes as $subType) {
                    $subType['active'] = 1;
                    $subType['order_type_id'] = $orderType->id;
                    SubType::create($subType);
                }
            }
        }
    }
}
