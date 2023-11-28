<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrateBattleStart implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $battleId;
    protected $seed;
    /**
     * Create a new event instance.
     */
    public function __construct($battleId, $seed)
    {
        $this->battleId = $battleId;
        $this->seed = $seed;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('CrateBattle.' . $this->battleId),
        ];
    }

    public function broadcastAs() {
        return "battle-start";
    }

    public function broadcastWith() {
        return ["seed" => $this->seed];
    }
}
