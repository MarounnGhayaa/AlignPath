<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferenceTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_cannot_store_preferences() {
        $response = $this->postJson('/api/v0.1/user/preferences', [
            'skills' => 'PHP',
            'interests' => 'Backend',
            'values' => 'Innovation',
            'careers' => 'Software Engineer',
        ]);

        $response->assertStatus(401); // unauthenticated
    }

    /** @test */
    public function it_validates_required_fields_when_storing_preferences() {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->postJson('/api/v0.1/user/preferences', []);

        $response->assertStatus(422) // validation error
                 ->assertJsonValidationErrors(['skills', 'interests', 'values', 'careers']);
    }

    /** @test */
    public function authenticated_user_can_store_preferences() {
        $user = User::factory()->create();

        $payload = [
            'skills' => 'PHP',
            'interests' => 'Backend',
            'values' => 'Innovation',
            'careers' => 'Software Engineer',
        ];

        $response = $this->actingAs($user, 'api')
                         ->postJson('/api/v0.1/user/preferences', $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment($payload);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'skills' => 'PHP',
            'interests' => 'Backend',
            'values' => 'Innovation',
            'careers' => 'Software Engineer',
        ]);
    }
}
