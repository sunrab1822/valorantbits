<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrateBattleJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $battleId = null;
    protected $joinedUser = null;
    protected $spot = null;
    /**
     * Create a new event instance.
     */
    public function __construct($battleId, $joinedUser, $spot)
    {
        $this->battleId = $battleId;
        $this->joinedUser = $joinedUser;
        $this->spot = $spot;
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
        return "battle-join";
    }

    public function broadcastWith() {
        $userData = [
            "profile_image" => $this->joinedUser->profile_image,
            "username" => $this->joinedUser->username,
            "is_bot" => $this->joinedUser->is_bot
        ];
        return ["joined_user" => $userData, "spot" => $this->spot];
    }
}
