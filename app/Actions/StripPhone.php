<?php

namespace App\Actions;

class StripPhone
{
    public function handle(string $phone): string
    {
        return str_replace(['+','(',')','-'],'',$phone);
    }
}
