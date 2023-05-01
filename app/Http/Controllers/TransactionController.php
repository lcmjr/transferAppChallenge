<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\API\TransactionService;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $payerId = $request->json('payer.id');
        $payeeId = $request->json('payee.id');
        $value = $request->float('value');
        $mesage = 'error';
        $code = 500;
        try {
            DB::beginTransaction();
            $walletPayer = Wallet::where('user_id', $payerId)->lockForUpdate()->get();
            $walletPayee = Wallet::where('user_id', $payeeId)->lockForUpdate()->get();
            if (!isset($walletPayer[0]) || !isset($walletPayee[0])) {
                DB::rollback();
                return response()->json(['message' => 'Ocorreu um erro em buscar os Usuários'], 409);
            }
            $walletPayer = $walletPayer[0];
            $walletPayee = $walletPayee[0];

            if (!$walletPayer->hasBalance($value)) {
                $mesage = 'Insuficient balance';
                $code = 409;
            }
            $isAuthorized = (new TransactionService())->authorizeTransaction();
            $transaction = Transaction::create([
                'value' => $value,
                'payer_id' => $payerId,
                'payee_id' => $payeeId,
                'was_authorized' => $isAuthorized,
                'was_notified' => false
            ]);
            if ($isAuthorized) {
                $walletPayer->balance -= $value;
                $walletPayee->balance += $value;
                $walletPayer->save();
                $walletPayee->save();
            }
            DB::commit();
            return TransactionResource::make($transaction);
        } catch (\Exception $exception) {
            DB::rollback();
        }

        return response()->json(['message' => $mesage], $code);
    }
}
