<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully() {
        $payload = [
            'username' => 'Maroun',
            'email' => 'maroun@example.com',
            'password' => 'password123',
            'role' => 'student',
        ];

        $response = $this->postJson('/api/v0.1/guest/register', $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('payload.username', 'Maroun')
                 ->assertJsonPath('payload.email', 'maroun@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'maroun@example.com',
            'username' => 'Maroun',
        ]);
    }

    #[Test]
    public function registration_requires_all_fields() {
        $response = $this->postJson('/api/v0.1/guest/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['username', 'email', 'password', 'role']);
    }

    #[Test]
    public function user_can_login_with_correct_credentials() {
        $user = User::factory()->create([
            'email' => 'maroun@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v0.1/guest/login', [
            'email' => 'maroun@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonStructure([
                     'status',
                     'payload' => ['id', 'username', 'email', 'role', 'token']
                 ]);
    }

    #[Test]
    public function login_fails_with_invalid_credentials() {
        $user = User::factory()->create([
            'email' => 'maroun@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v0.1/guest/login', [
            'email' => 'maroun@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'payload' => null,
                 ]);
    }
}
