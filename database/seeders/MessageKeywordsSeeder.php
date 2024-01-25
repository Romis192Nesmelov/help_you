<?php

namespace Database\Seeders;
use App\Models\MessageKeyword;
use Illuminate\Database\Seeder;

class MessageKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phrases = [
            'закрыть заявку',
            'закрывать заявку',
            'закрыть запрос',
            'закрывать запрос',
            'все прекрасно',
            'хорошая работа'
        ];

        foreach ($phrases as $phrase) {
            MessageKeyword::create(['phrase' => $phrase]);
        }
    }
}
