<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CoinflipJoin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $Coinflip = null;
    protected $User = null;
    protected $joined_side = null;
    /**
     * Create a new event instance.
     */
    public function __construct($Coinflip, $User, $joined_side)
    {
        $this->Coinflip = $Coinflip;
        $this->User = $User;
        $this->joined_side = $joined_side;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Coinflip.' . $this->Coinflip->id),
        ];
    }

    public function broadcastAs() {
        return "coinflip-join";
    }

    public function broadcastWith() {
        return ["opponent" => $this->User, "side" => $this->joined_side, "game_state" => $this->Coinflip->game_state];
    }
}
