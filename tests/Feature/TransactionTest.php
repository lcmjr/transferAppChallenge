<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransactionTest extends TestCase
{

    protected function setUp(): void
    {
        $this->mockCall();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * A basic feature test example.
     */
    public function testTransactionSuccess(): void
    {
        $payer = $this->getRegularUser();
        $payee = $this->getUserStore();
        $response = $this->postJson('/api/transaction', [
            'value' => 10,
            'payer' => ['id' => $payer->id],
            'payee' => ['id' => $payee->id],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.payer.wallet.balance', 990);
        $response->assertJsonPath('data.payee.wallet.balance', 1010);
        $payer->delete();
        $payee->delete();
    }

    /**
     * A basic feature test example.
     */
    public function testTransactionInsufficientBalance(): void
    {
        $payer = $this->getRegularUser();
        $payee = $this->getUserStore();
        $response = $this->postJson('/api/transaction', [
            'value' => 1001,
            'payer' => ['id' => $payer->id],
            'payee' => ['id' => $payee->id],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.value.0', "Insuficient Balance");
        $payer->delete();
        $payee->delete();
    }

    /**
     * A basic feature test example.
     */
    public function testTransactionUserNotAllowed(): void
    {
        $payer = $this->getRegularUser();
        $payee = $this->getUserStore();
        $response = $this->postJson('/api/transaction', [
            'value' => 1001,
            'payee' => ['id' => $payer->id],
            'payer' => ['id' => $payee->id],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payer.id');
        $payer->delete();
        $payee->delete();
    }

    /**
     * A basic feature test example.
     */
    public function testTransactionBetweenSameUser(): void
    {
        $payer = $this->getRegularUser();
        $response = $this->postJson('/api/transaction', [
            'value' => 10,
            'payee' => ['id' => $payer->id],
            'payer' => ['id' => $payer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payee.id');
        $payer->delete();
    }

    /**
     * A basic feature test example.
     */
    public function testTransactionBetweenUsers(): void
    {
        $payer = $this->getRegularUser();
        $payee = $this->getRegularUser(
            "teste Payer2",
            "teste.payer2@teste.com",
            "99999999998"
        );
        $payer->document = "99999999998";
        $response = $this->postJson('/api/transaction', [
            'value' => 10,
            'payee' => ['id' => $payer->id],
            'payer' => ['id' => $payer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payee.id');
        $payer->delete();
    }

    private function getUserStore(): User
    {
        $user = User::updateOrCreate([
            'document' => "99999999999989",
        ],
            [
                'name' => "teste Payee",
                'email' => "teste.payee@teste.com",
                'password' => Hash::make("#Este12345"),
                'type' => User::USER_STORE_TYPE
            ]);
        $user->wallet->balance = 1000;
        $user->wallet->save();
        return $user;
    }

    private function getRegularUser(
        $name = "teste Payer",
        $email = "teste.payer@teste.com",
        $document = "99999999999"
    ): User {
        $user = User::updateOrCreate([
            'document' => $document
        ],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make("#Este12345"),
                'type' => User::USER_REGULAR_TYPE
            ]);
        $user->wallet->balance = 1000;
        $user->wallet->save();
        return $user;
    }

    private function mockCall()
    {
        Http::fake([
                env('AUTHORIZATION_API_URL') =>
                    Http::response(['message' => 'Autorizado']),
                env('NOTIFY_API_URL') =>
                    Http::response(['message' => 'Success'])
            ]
        );
    }
}
