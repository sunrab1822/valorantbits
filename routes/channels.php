<?php

use App\Broadcasting\CoinflipChannel;
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

Broadcast::channel('App.Models.CrateBattle.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('Coinflip.{id}', CoinflipChannel::class);

Broadcast::channel('CoinflipList', function (User $user) {return true;});

