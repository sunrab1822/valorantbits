<?php

namespace App\Jobs;

use App\Events\CrateBattleCompleted;
use App\Models\CrateBattle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        broadcast(new CrateBattleCompleted($this->CrateBattle->id));
    }
}
