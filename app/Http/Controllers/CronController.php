<?php

namespace App\Http\Controllers;

use App\Models\Order;

class CronController extends Controller
{
    use HelperTrait;

    public function daily()
    {
        $this->checkOrdersInProgress();
    }
}
