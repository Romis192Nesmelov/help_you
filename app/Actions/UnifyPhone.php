<?php

namespace App\Actions;

class UnifyPhone
{
    public function handle(string $phone): string
    {
        return '+7'.substr($phone,2);
    }
}
