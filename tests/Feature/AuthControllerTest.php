<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the register method.
     */
    public function test_user_registration_successfully()
    {
        // Step 1: Define registration data
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Step 2: Make POST request to the register endpoint
        $response = $this->postJson(route('register'), $requestData);

        // Step 3: Assert response status and structure
        $response->assertStatus(201)
            ->assertJson([
                'user_created' => __('messages.user_registered')
            ]);

        // Step 4: Assert the user is created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        // Optional: Ensure the password is hashed
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test the login method successfully.
     */
    public function test_user_login_successfully()
    {
        // Step 1: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Step 2: Define login credentials
        $requestData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Step 3: Make POST request to the login endpoint
        $response = $this->postJson(route('login'), $requestData);

        // Step 4: Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        // Add more user fields if necessary
                    ]
                ]
            ]);

        // Step 5: Assert the token is created
        $this->assertNotNull($user->tokens()->first());
    }

    /**
     * Test invalid login credentials.
     */
    public function test_user_login_with_invalid_credentials()
    {
        // Step 1: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Step 2: Define incorrect login credentials
        $requestData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Step 3: Make POST request to the login endpoint
        $response = $this->postJson(route('login'), $requestData);

        // Step 4: Assert the response status and structure
        $response->assertStatus(401)
            ->assertJson([
                'message' => __('messages.Invalid_login_credentials'),
                'data' => []
            ]);
    }
}
