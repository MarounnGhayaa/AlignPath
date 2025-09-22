<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Services\Common\AuthService;
use PHPUnit\Framework\Attributes\Test;

class AuthServiceTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function it_registers_a_user() {
        $request = new Request([
            'username' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $user = AuthService::register($request);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'John Doe',
            'role' => 'student',
        ]);

        $this->assertNotNull($user->token);
    }

    #[Test]
    public function login_returns_user_with_correct_credentials() {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $request = new Request([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $loggedInUser = AuthService::login($request);

        $this->assertNotNull($loggedInUser);
        $this->assertEquals($user->email, $loggedInUser->email);
        $this->assertNotNull($loggedInUser->token);
    }

    #[Test]
    public function login_returns_null_with_wrong_credentials() {
        $request = new Request([
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $user = AuthService::login($request);

        $this->assertNull($user);
    }
}
