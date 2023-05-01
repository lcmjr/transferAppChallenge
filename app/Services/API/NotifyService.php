<?php

namespace App\Services\API;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyService
{
    public function notifyTransaction(Transaction $transaction)
    {
        $transaction->load('payee');
        try {
            $response = Http::get(env('NOTIFY_API_URL'))->throw();
            if ($response->json('message') === "Success") {
                $transaction->was_notified = true;
                $transaction->save();
                Log::info('Transaction notified with success', [
                    'transactionId' => $transaction->id
                ]);
            }
            Log::info('Transaction notified with success', [
                'transactionId' => $transaction->id
            ]);
        } catch (\Exception $exception) {
            Log::error('Error on notify Transaction', [
                'transactionId' => $transaction->id,
                'exception' => $exception
            ]);
        }
    }
}
