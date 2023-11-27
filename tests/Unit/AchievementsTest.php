<?php

namespace Tests\Unit;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to ensure the created Achievement has all required attributes.
     */
    public function test_it_has_all_attributes(): void
    {
        // Create an Achievement instance using the factory
        $achievement = Achievement::factory()->create();

        // Check if the created Achievement instance has all the required attributes
        $this->assertNotNull($achievement->id);
        $this->assertNotNull($achievement->name);
        $this->assertNotNull($achievement->value);
    }
    public function test_unlock_achievement()
    {
        // Create a user and an achievement
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create();

        // Call the unlockAchievement method
        $achievement->unlockAchievement($user);

        // Assert that the user has the achievement attached
        $this->assertTrue($user->achievements->contains($achievement));
    }
    public function test_get_achievements_scope()
    {
        // Create a user and some achievements
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(5)->create();

        // Attach a few achievements to the user
        $user->achievements()->attach($achievements->pluck('id'));

        // Call the scopeGetAchievements method
        $result = Achievement::getAchievements($user);

        // Assert the structure of the result
        $this->assertArrayHasKey('unlocked_achievements', $result);
        $this->assertArrayHasKey('next_available_achievements', $result);
        $this->assertArrayHasKey('current_badge', $result);
        $this->assertArrayHasKey('next_badge', $result);
        $this->assertArrayHasKey('remaing_to_unlock_next_badge', $result);

    }
    public function test_achievements_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create();

        // Attach the user to the achievement using the pivot table
        $achievement->users()->attach($user);

        // Retrieve the associated users using the relationship
        $associatedUsers = $achievement->users;

        $this->assertInstanceOf(User::class, $associatedUsers->first());
        $this->assertEquals($user->id, $associatedUsers->first()->id);
    }
}
