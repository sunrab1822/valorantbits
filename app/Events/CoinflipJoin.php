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

    protected $id = null;
    protected $user = null;
    protected $joined_side = null;
    /**
     * Create a new event instance.
     */
    public function __construct($id, $user, $joined_side)
    {
        $this->id = $id;
        $this->user = $user;
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
            new PrivateChannel('App.Models.Coinflip.' . $this->id),
        ];
    }

    public function broadcastAs() {
        return "coinflip-join";
    }

    public function broadcastWith() {
        return ["joined_user" => $this->user->username, "side" => $this->joined_side, "user_profile" => $this->user->profile_image];
    }
}
