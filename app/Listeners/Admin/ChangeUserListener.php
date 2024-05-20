<?php

namespace App\Listeners\Admin;

use App\Events\Admin\AdminUserEvent;
use function broadcast;

class ChangeUserListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        broadcast(new AdminUserEvent('change_item',$event->user));
    }
}
