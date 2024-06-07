<?php

namespace App\Listeners\Admin;

use App\Events\Admin\AdminUserEvent;
use App\Http\Controllers\MessagesHelperTrait;
use function broadcast;

class NewUserListener
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
        $this->sendSms(str_replace(['+','(',')','-'],'',$event->user->phone),$event->user->code);
        broadcast(new AdminUserEvent('new_item',$event->user));
    }
}
