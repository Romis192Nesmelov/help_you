<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\ActionUser;
use Illuminate\Database\Seeder;

class ActionsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = Action::pluck('id')->toArray();
        foreach ($actions as $id) {
            ActionUser::create([
                'action_id' => $id,
                'user_id' => 2,
                'active' => 1
            ]);
        }
    }
}
