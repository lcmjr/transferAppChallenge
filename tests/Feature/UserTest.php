<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateUser(): void
    {
        $this->deleteTestUsers('test@test.com',"99999999999989");

        $response = $this->postJson('/api/user',[
            'name' => "test",
            'email' => 'test@test.com',
            'document' => "99999999999989",
            'password' => "#Este12345",
            'password_confirmation' => "#Este12345",
        ]);
        $response->assertStatus(201);
    }

    /**
     * A basic feature test example.
     */
    public function testUserCreateValidation(): void
    {
        $response = $this->postJson('/api/user');
        $response->assertStatus(422);
        $response->assertJsonPath('errors.name.0', "The name field is required.");
        $response->assertJsonPath('errors.email.0', "The email field is required.");
        $response->assertJsonPath('errors.document.0', "The document field is required.");
        $response->assertJsonPath('errors.password.0', "The password field is required.");
        $this->deleteTestUsers("teste.validation@teste.com","99999999999989");

        $user = User::updateOrCreate([
            'name' => "teste Validation",
            'email' => "teste.validation@teste.com",
            'document' => "99999999999989",
            'password' => Hash::make("#Este12345"),
            'type' => User::USER_STORE_TYPE
        ]);
        $response = $this->postJson('api/user',[
            'name' => "teste Validation",
            'email' => "teste.validation@teste.com",
            'document' => "99999999999989",
            'password' => "#Este12345",
            'password_confirmation' => "#Este12345",
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('errors.email.0', "The email has already been taken.");
        $response->assertJsonPath('errors.document.0', "The document has already been taken.");
    }

    private function deleteTestUsers($email='test@test.com',$document="teste.validation@teste.com")
    {
        $users = User::where('email',$email)->orWhere('document',$document)->get();
        foreach($users as $user){
            $user->delete();
        }
    }
}
