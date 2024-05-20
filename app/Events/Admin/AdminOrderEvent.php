<?php

namespace App\Events\Admin;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminOrderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private string $noticeType;
    private Order $order;

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
            new Channel('admin_order_event'),
        ];
    }

    /**
     *  The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'admin_order';
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
            'model' => $this->order->with(['user']),
        ];
    }
}
