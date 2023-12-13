<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CoinflipCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $Coinflip;
    /**
     * Create a new event instance.
     */
    public function __construct($Coinflip)
    {
        $this->Coinflip = $Coinflip;
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
        return "coinflip-created";
    }

    public function broadcastWith() {
        if($this->Coinflip->heads){
            $this->Coinflip->userHeads;
        }
        if($this->Coinflip->tails){
            $this->Coinflip->userTails;
        }
        return ["coinflip" => $this->Coinflip->toArray()];
    }
}
