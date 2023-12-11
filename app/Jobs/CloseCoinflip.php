<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseCoinflip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $Coinflip = null;
    /**
     * Create a new job instance.
     */
    public function __construct($Coinflip)
    {
        $this->Coinflip = $Coinflip;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->Coinflip->game_state = 2;
        $this->Coinflip->save();
    }
}
