<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Services\Users\User_PreferencesService;
use PHPUnit\Framework\Attributes\Test;

class UserPreferencesServiceTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function it_stores_user_preferences() {
        $user = User::factory()->create();

        $request = new Request([
            'skills' => 'PHP, Laravel',
            'interests' => 'AI, Web',
            'values' => 'Innovation',
            'careers' => 'Software Engineer',
        ]);

        $preference = User_PreferencesService::storePreferences($request, $user->id);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'skills' => 'PHP, Laravel',
            'interests' => 'AI, Web',
            'values' => 'Innovation',
            'careers' => 'Software Engineer',
        ]);

        $this->assertInstanceOf(UserPreference::class, $preference);
    }
}
