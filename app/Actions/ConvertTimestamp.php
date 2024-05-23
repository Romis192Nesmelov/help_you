<?php

namespace App\Actions;
use Carbon\Carbon;

class ConvertTimestamp
{
    public function handle(string $time): Carbon
    {
        $time = explode('/', $time);
        return Carbon::createFromTimestamp(strtotime($time[1].'/'.$time[0].'/'.$time[2]));
    }
}
