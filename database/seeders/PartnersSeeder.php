<?php

namespace Database\Seeders;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Ali Express',
            'Авто-ремонтные системы',
            'Азбука вкуса',
            'Bauer',
            'Beer Trade',
            'Деловые линии',
            'Еврологистика',
            'Фирма К и К',
            'Лидер',
            'MARR Russia',
            'Департамент физической культуры и спорта города Москвы',
            'Очаково. Натуральные напитки',
            'Rubezh Global'
        ];

        foreach ($data as $k => $partner) {
            Partner::create([
                'logo' => 'images/partners/logo'.($k + 1).'.svg',
                'name' => $partner
            ]);
        }
    }
}
