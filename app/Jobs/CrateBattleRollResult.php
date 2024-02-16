<?php

namespace App\Jobs;

use App\Events\CrateBattleRoll;
use App\Models\Crate;
use App\Models\Skin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CrateBattleRollResult implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $CrateBattle;
    protected $crate_number;
    /**
     * Create a new job instance.
     */
    public function __construct($CrateBattle, $crate_number)
    {
        $this->CrateBattle = $CrateBattle;
        $this->crate_number = $crate_number;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crate_list = json_decode($this->CrateBattle->crates, true);
        $number_of_players = $this->CrateBattle->getNumberOfSpots($this->CrateBattle->battle_type);
        $result = $this->CrateBattle->result ? json_decode($this->CrateBattle->result, true) : [];
        $result_items = [];

        $Crate = Crate::find($crate_list[$this->crate_number]);
        $chances = array_map(function($item){ return $item['chance']; }, $Crate->contents->toArray());
        $items = array_map(function($item){ return $item['skin_id']; }, $Crate->contents->toArray());

        for($i = 0; $i < $number_of_players; $i++) {
            $wonItemId = $this->CrateBattle->determineItemOutcome($this->CrateBattle->seed, $chances, $items, $this->crate_number+1, $i+1);
            $result[$this->crate_number][$i] = $wonItemId;
            $result_items[] = Skin::find($wonItemId);
        }

        $this->CrateBattle->result = $result;
        $this->CrateBattle->save();

        broadcast(new CrateBattleRoll($this->CrateBattle->id, $result_items, $this->crate_number));
        if($this->crate_number + 1 < count($crate_list)) {
            $this->dispatch($this->CrateBattle, $this->crate_number + 1)->delay(now()->addSeconds(8))->onQueue("battle");
        } else {
            CloseCrateBattle::dispatch($this->CrateBattle)->delay(now()->addSeconds(8))->onQueue("battle");
        }
    }
}
