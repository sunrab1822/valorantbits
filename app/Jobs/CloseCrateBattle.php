<?php

namespace App\Jobs;

use App\Events\CrateBattleCompleted;
use App\Models\CrateBattle;
use App\Models\Skin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function Symfony\Component\String\b;

class CloseCrateBattle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $CrateBattle;
    /**
     * Create a new job instance.
     */
    public function __construct($CrateBattle)
    {
        $this->CrateBattle = $CrateBattle;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->CrateBattle->game_state = 2;
        $this->CrateBattle->save();

        $player_list = json_decode($this->CrateBattle->players);
        $result = json_decode($this->CrateBattle->result);
        $players = User::select(["id", "username", "profile_image", "is_bot"])->whereIn("id", array_values($player_list))->get()->sortBy(function($item) use ($player_list) {
            return array_search($item->id, $player_list);
        })->values();

        $playerAmounts = [];

        foreach($result as $round) {
            foreach($result[$round] as $row) {
                $skin = Skin::find($result[$round][$row]);
                if(isset($playerAmounts[$row])) {
                    $playerAmounts[$row] += $skin->price;
                } else {
                    $playerAmounts[$row] = $skin->price;
                }
            }
        }

        if($this->CrateBattle->battle_type == 4) {

        } else {

        }

        foreach($players as $player) {

        }

        broadcast(new CrateBattleCompleted($this->CrateBattle->id));
    }
}
