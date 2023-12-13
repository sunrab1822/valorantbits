<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CoinflipCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $coinflipId;
    /**
     * Create a new event instance.
     */
    public function __construct($coinflipId)
    {
        $this->coinflipId = $coinflipId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Coinflips'),
        ];
    }

    public function broadcastAs() {
        return "coinflip-completed";
    }

    public function broadcastWith() {
        return ["id" => $this->coinflipId];
    }
}
