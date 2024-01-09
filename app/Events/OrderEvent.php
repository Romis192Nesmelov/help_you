<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $noticeType;
    private int $orderId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, int $orderId)
    {
        $this->noticeType = $noticeType;
        $this->orderId = $orderId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order_event'),
        ];
    }

    /**
     *  The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order';
    }

    /**
     *  Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'notice' => $this->noticeType,
            'order_id' => $this->orderId
        ];
    }
}
