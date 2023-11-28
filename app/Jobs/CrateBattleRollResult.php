<?php

namespace App\Jobs;

use App\Events\CrateBattleRoll;
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

    protected $result;
    protected $CrateBattle;
    protected $crate_number;
    /**
     * Create a new job instance.
     */
    public function __construct($CrateBattle, $result, $crate_number)
    {
        $this->result = $result;
        $this->CrateBattle = $CrateBattle;
        $this->crate_number = $crate_number;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $result_set = [];
        foreach($this->result[$this->crate_number + 1] as $result) {
            $result_set[] = Skin::find($result);
        }
        broadcast(new CrateBattleRoll($this->CrateBattle->id, $result_set, $this->crate_number));
        if($this->crate_number + 1 < count($this->result)) {
            $this->dispatch($this->CrateBattle, $this->result, $this->crate_number + 1)->delay(now()->addSeconds(10))->onQueue("battle");
        }
    }
}
