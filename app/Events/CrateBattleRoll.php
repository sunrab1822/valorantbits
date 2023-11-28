<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrateBattleRoll implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $battleId;
    protected $result;
    protected $crate_number;
    /**
     * Create a new event instance.
     */
    public function __construct($battleId, $result, $crate_number)
    {
        $this->battleId = $battleId;
        $this->result = $result;
        $this->crate_number = $crate_number;
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
        return "battle-roll";
    }

    public function broadcastWith() {
        return ["result" => $this->result, "crate_number" => $this->crate_number];
    }
}
