<?php

namespace App\Actions;

class ProcessingSpecialFields
{
    public function handle($fields, $specFieldName): array
    {
        if (request()->has($specFieldName)) $fields[$specFieldName] = 1;
        else $fields[$specFieldName] = 0;

        return $fields;
    }
}
