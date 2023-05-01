<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function authorizeTransaction(): bool
    {
        try {
            $response = Http::get(env('AUTHORIZATION_API_URL'))->throw();
            if ($response->json('message') == 'Autorizado') {
                return true;
            }
            Log::error('Transaction not Autorized', ['response' => $response->json()]);
        } catch (\Exception $exception) {
            Log::error('Transaction Authorization Error', ['exception' => $exception]);
        }
        return false;
    }
}
