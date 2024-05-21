<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SetReadUnreadUser
{
    public function handle(Model $model): void
    {
        $model->query()
            ->where('user_id',Auth::id())
            ->where('read',null)
            ->update(['read' => true]);
    }
}
