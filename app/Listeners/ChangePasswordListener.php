<?php

namespace App\Listeners;
use App\Http\Controllers\MessagesHelperTrait;

class ChangePasswordListener
{
    use MessagesHelperTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @throws \Exception
     */
    public function handle(object $event): void
    {
        $this->sendSms($event->user->phone, 'Ваш новый пароль: '.$event->password);
    }
}
