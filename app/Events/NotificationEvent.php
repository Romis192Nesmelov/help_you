<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $noticeType;
    private Order $order;
    private int $userId;
    private int|null $subscriptionId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, Order $order, int $userId, int|null $subscriptionId=null)
    {
        $this->noticeType = $noticeType;
        $this->order = $order;
        $this->userId = $userId;
        $this->subscriptionId = $subscriptionId;
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
        return [
            'notice' => $this->noticeType,
            'order' => $this->order,
            'performers' => $this->order->performers,
            'user' => $this->order->user,
            'order_type' => $this->order->orderType,
            'subscription_id' => $this->subscriptionId
        ];
    }
}
