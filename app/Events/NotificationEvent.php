<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $noticeType;
    private int $userId;
    private int $orderId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, int $orderId, int $userId=null)
    {
        $this->noticeType = $noticeType;
        $this->orderId = $orderId;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notice_'.$this->userId),
        ];
    }

    /**
     *  The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notice';
    }

    /**
     *  Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        if ($this->userId) {
            $user = User::find($this->userId);
            return [
                'notice' => $this->noticeType,
                'order_id' => $this->orderId,
                'user_name' => $user->name.' '.$user->family
            ];
        } else {
            return [
                'notice' => $this->noticeType,
                'order_id' => $this->orderId
            ];
        }
    }
}
