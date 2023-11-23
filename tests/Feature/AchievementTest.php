<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_lesson_watched_achievement_is_unlocked()
    {
        Event::fake(AchievementUnlocked::class);

        //generate user, lesson and lesson_user relationship
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->watched()->attach($lesson->id, ['watched' => true]);

        //listen lesson watched event
        event(new LessonWatched($lesson, $user));

        //test Achievement Unlocked event
        Event::assertDispatched(AchievementUnlocked::class);

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);

    }

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
