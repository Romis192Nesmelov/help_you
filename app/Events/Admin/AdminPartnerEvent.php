<?php

namespace App\Events\Admin;

use App\Models\Partner;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminPartnerEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private string $noticeType;
    private Partner $partner;

    public function __construct(string $noticeType, Partner $partner)
    {
        $this->noticeType = $noticeType;
        $this->partner = $partner;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin_partner_event'),
        ];
    }

    /**
     *  The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'admin_partner';
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
            'model' => $this->partner,
        ];
    }
}
