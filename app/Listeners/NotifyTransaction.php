<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\API\NotifyService;

class NotifyTransaction
{
    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->getTransaction();
        if (!$transaction->isValid()) {
            return;
        }
        (new NotifyService())->notifyTransaction($transaction);
    }
}
