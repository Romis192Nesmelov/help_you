<?php

namespace App\Http\Controllers;

trait FieldsHelperTrait
{
    public array $orderLoadFields = ['user.ratings','orderType','subType','performers','images'];
    public array $actionLoadFields = ['users','partner','actionUsers'];
}
