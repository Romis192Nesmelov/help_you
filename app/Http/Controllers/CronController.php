<?php

namespace App\Http\Controllers;

class CronController extends Controller
{
    use MessagesHelperTrait;

    public function daily()
    {
        $this->checkOrdersInProgress();
        $this->checkRegistrationAward();
    }
}
