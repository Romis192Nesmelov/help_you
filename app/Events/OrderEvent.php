<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $noticeType;
    private Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, Order $order)
    {
        $this->noticeType = $noticeType;
        $this->order = $order;
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
            'order' => $this->order,
            'performers' => $this->order->performers,
            'user' => $this->order->user,
            'order_type' => $this->order->orderType,
            'sub_type' => $this->order->subType,
            'images' => $this->order->images,
        ];
    }
}
