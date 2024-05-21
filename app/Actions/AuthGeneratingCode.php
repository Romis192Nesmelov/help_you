<?php

namespace App\Actions;

class AuthGeneratingCode
{
    public function handle(): string
    {
        return rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9);
    }
}
