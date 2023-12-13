<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrateBattleCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $crateBattleId;
    /**
     * Create a new event instance.
     */
    public function __construct($crateBattleId)
    {
        $this->crateBattleId = $crateBattleId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('CrateBattles'),
        ];
    }

    public function broadcastAs() {
        return "battle-completed";
    }

    public function broadcastWith() {
        return ["battle" => $this->crateBattleId];
    }
}
