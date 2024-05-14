<?php

namespace App\Events;

use App\Models\ActionUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncentivesEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $noticeType;
    private ActionUser $incentive;
    private int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $noticeType, ActionUser $incentive, int $userId)
    {
        $this->noticeType = $noticeType;
        $this->incentive = $incentive;
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
            new PrivateChannel('incentive_'.$this->userId),
        ];
    }

    /**
     *  The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'incentive';
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
            'incentive' => $this->incentive,
            'action' => $this->incentive->action,
        ];
    }
}
