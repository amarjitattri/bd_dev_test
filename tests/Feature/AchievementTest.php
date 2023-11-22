<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_assigned_to_achievement(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create an achievement
        $achievement = Achievement::factory()->create();

        // Assign the achievement to the user
        $achievement->awardTo($user);

        // Assert that the user has the achievement
        $this->assertCount(1, $user->achievements);
        $this->assertTrue($user->achievements->contains($achievement));
    }
}
