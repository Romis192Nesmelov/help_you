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

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, Order $order, int $userId)
    {
        $this->noticeType = $noticeType;
        $this->order = $order;
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
        if ($this->noticeType == 'new_order') {
            return [
                'notice' => $this->noticeType,
                'order_id' => $this->order->id,
                'user_name' => $this->order->user->name . ' ' . $this->order->user->family
            ];
        } elseif ($this->noticeType == 'new_performer') {
            return [
                'notice' => $this->noticeType,
                'order_id' => $this->order->id,
                'user_name' => $this->order->performers[$this->order->performers->count()-1]->name . ' ' . $this->order->performers[$this->order->performers->count()-1]->family
            ];
        } else {
            return [
                'notice' => $this->noticeType,
                'order_id' => $this->order->id,
                'performers' => $this->order->performers->count(),
                'order_status' => $this->order->status,
            ];
        }
    }
}
