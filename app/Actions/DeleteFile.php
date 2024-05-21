<?php

namespace App\Actions;

class DeleteFile
{
    public function handle(string $path): void
    {
        if (file_exists(base_path('public/'.$path))) unlink(base_path('public/'.$path));
    }
}
