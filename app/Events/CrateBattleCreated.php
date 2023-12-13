<?php

namespace App\Events;

use App\Models\Crate;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrateBattleCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $CrateBattle;
    /**
     * Create a new event instance.
     */
    public function __construct($CrateBattle)
    {
        $this->CrateBattle = $CrateBattle;
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
        return "battle-created";
    }

    public function broadcastWith() {
        $crates = json_decode($this->CrateBattle->crates);
        $players = json_decode($this->CrateBattle->players);
        $this->CrateBattle->crate_list = Crate::find(array_values($crates))->sortBy(function($item) use ($crates) {
            return array_search($item->id, $crates);
        })->values();
        $this->CrateBattle->player_list = User::select(["id", "username", "profile_image", "is_bot"])->whereIn("id", array_values($players))->get()->sortBy(function($item) use ($players) {
            return array_search($item->id, $players);
        })->values();
        return ["battle" => $this->CrateBattle->toArray()];
    }
}
