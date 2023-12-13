<?php

namespace App\Jobs;

use App\Events\CoinflipCompleted;
use App\Models\User;
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
        broadcast(new CoinflipCompleted($this->Coinflip->id));
        $User = null;

        if($this->Coinflip->created_by == "heads") {
            if($this->Coinflip->chance_float < $this->Coinflip->created_float) {
                $User = User::find($this->Coinflip->heads);
            } else {
                $User = User::find($this->Coinflip->tails);
            }
        } else {
            if($this->Coinflip->chance_float > 1 - $this->Coinflip->created_float) {
                $User = User::find($this->Coinflip->tails);
            } else {
                $User = User::find($this->Coinflip->heads);
            }
        }

        if(!$User->is_bot) {
            $User->balance += $this->Coinflip->heads_amount + $this->Coinflip->tails_amount;
            $User->save();
        }
    }
}
