<?php

namespace App\Http\Controllers;

class CronController extends Controller
{
    use HelperTrait;

    public function daily()
    {
        $this->checkOrdersInProgress();
        $this->checkRegistrationAward();
    }
}
