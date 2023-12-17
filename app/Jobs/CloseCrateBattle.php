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

        $player_list = json_decode($this->CrateBattle->players, true);
        $result = json_decode($this->CrateBattle->result, true);
        $players = User::whereIn("id", array_values($player_list))->get()->sortBy(function($item) use ($player_list) {
            return array_search($item->id, $player_list);
        })->values();

        $playerAmounts = [];
        $terminalAmounts = [];

        foreach($result as $k => $round) {
            foreach($round as $j => $row) {
                $skin = Skin::find($result[$k][$j]);
                if(isset($playerAmounts[$j])) {
                    $playerAmounts[$j] += $skin->price;
                } else {
                    $playerAmounts[$j] = $skin->price;
                }

                if(count($result) - 1 == $k) {
                    $terminalAmounts[$j] = $skin->price;
                }
            }
        }

        if($this->CrateBattle->is_group) {
            $amount = array_sum($playerAmounts) / count($players);
            foreach($players as $player) {
                if($player->is_bot) continue;
                $player->balance += $amount;
                $player->save();
            }
        } else {
            if($this->CrateBattle->battle_type == 4) {
                $team_one = ($this->CrateBattle->is_terminal) ? $terminalAmounts[0] + $terminalAmounts[1] : $playerAmounts[0] + $playerAmounts[1];
                $team_two = ($this->CrateBattle->is_terminal) ? $terminalAmounts[2] + $terminalAmounts[3] : $playerAmounts[2] + $playerAmounts[3];
                $amount_to_give = array_sum($playerAmounts) / 2;

                if($team_one == $team_two) {
                    if($this->CrateBattle->tie_float < 0.5) {
                        if(!$players[0]->is_bot) {
                            $players[0]->balance += $amount_to_give;
                            $players[0]->save();
                        }

                        if(!$players[1]->is_bot) {
                            $players[1]->balance += $amount_to_give;
                            $players[1]->save();
                        }
                    } else {
                        if(!$players[2]->is_bot) {
                            $players[2]->balance += $amount_to_give;
                            $players[2]->save();
                        }

                        if(!$players[3]->is_bot) {
                            $players[3]->balance += $amount_to_give;
                            $players[3]->save();
                        }
                    }
                } else if($team_one > $team_two) {
                    if(!$players[0]->is_bot) {
                        $players[0]->balance += $amount_to_give;
                        $players[0]->save();
                    }

                    if(!$players[1]->is_bot) {
                        $players[1]->balance += $amount_to_give;
                        $players[1]->save();
                    }
                } else {
                    if(!$players[2]->is_bot) {
                        $players[2]->balance += $amount_to_give;
                        $players[2]->save();
                    }

                    if(!$players[3]->is_bot) {
                        $players[3]->balance += $amount_to_give;
                        $players[3]->save();
                    }
                }
            } else {
                if($this->CrateBattle->is_terminal) {
                    $highestAmount = max($terminalAmounts);
                    $sameAmounts = array_filter($terminalAmounts, function($item) use ($highestAmount) {
                        return $item == $highestAmount;
                    });
                    $player_index = array_search($highestAmount, $terminalAmounts);
                } else {
                    $highestAmount = max($playerAmounts);
                    $sameAmounts = array_filter($playerAmounts, function($item) use ($highestAmount) {
                        return $item == $highestAmount;
                    });
                    $player_index = array_search($highestAmount, $playerAmounts);
                }

                if(count($sameAmounts) == 1) {
                    if(!$players[$player_index]->is_bot) {
                        $players[$player_index]->balance += array_sum($playerAmounts);
                        $players[$player_index]->save();
                    }
                } else {
                    $player_indexes = array_keys($sameAmounts);
                    $won_player_index = floor($this->CrateBattle->tie_float * count($player_indexes));
                    if(!$players[$player_index]->is_bot) {
                        $players[$player_indexes[$won_player_index]]->balance += array_sum($playerAmounts);
                        $players[$player_indexes[$won_player_index]]->save();
                    }
                }
            }
        }

        broadcast(new CrateBattleCompleted($this->CrateBattle->id));
    }
}
