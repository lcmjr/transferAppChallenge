<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Wallet;

class CreateUserWallet
{
    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->getUser();
        $user->wallet()->create(['balance'=>Wallet::INITIAL_BALANCE]);
    }
}
