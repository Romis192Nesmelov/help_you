<?php

namespace App\Events;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class ChangePasswordEvent
{
    use SerializesModels;

    public User $user;
    public string $password;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
}
