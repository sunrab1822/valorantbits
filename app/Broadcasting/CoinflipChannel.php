<?php

namespace App\Broadcasting;

use App\Models\Coinflip;
use App\Models\User;

class CoinflipChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        return [$user];
    }
}
