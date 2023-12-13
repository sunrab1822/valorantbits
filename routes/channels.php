<?php

use App\Broadcasting\CoinflipChannel;
use App\Broadcasting\CrateBattleChannel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('CrateBattle.{id}', CrateBattleChannel::class);
Broadcast::channel('CrateBattles', CrateBattleChannel::class);
Broadcast::channel('Coinflip.{id}', CoinflipChannel::class);
Broadcast::channel('Coinflips', CoinflipChannel::class);

