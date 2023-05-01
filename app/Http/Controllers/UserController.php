<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $document = preg_replace("/\D/", "", $request->input('document'));
        $type = (strlen($document) == 11) ? User::USER_REGULAR_TYPE : User::USER_STORE_TYPE;

        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'document' => $document,
            'type' => $type,
            'password' => Hash::make($request->input('password')),
        ]);
    }
}
