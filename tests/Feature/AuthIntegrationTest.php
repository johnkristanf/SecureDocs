<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthIntegrationTest extends TestCase
{
    
    public function test_register(): void
    {

        $response = $this->post('/register', [
            'fullname' => 'testfullname',
            'email' => 'testemail@example.com',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword'
        ]);

        $this->assertDatabaseHas('users', [
            'fullname' => 'testfullname',
            'email' => 'testemail@example.com',
        ]);

        // Fetch the user and verify the password is hashed correctly
        $this->assertTrue(Hash::check('testpassword', User::where('email', 'testemail@example.com')->first()['password'] ));

        $response->assertRedirect(route('login.page'));

    }
}
