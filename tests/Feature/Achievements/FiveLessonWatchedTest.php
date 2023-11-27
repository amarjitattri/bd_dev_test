<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\FiveLessonsWatched;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FiveLessonWatchedTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_watch_first_five_lesson()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();

        // Create an achievement and associate it with the user
        $lesson = Lesson::factory()->count(5)->create();
        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        // Create an event and trigger the event listener
        $fiveLessonWatch = new FiveLessonsWatched();
        $result = $fiveLessonWatch->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($fiveLessonWatch, $user) {
            return $event->achievement_name === $fiveLessonWatch->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_has_not_watch_first_five_lesson()
    {
        Event::fake(AchievementUnlocked::class);

        // Create a user for testing
        $user = User::factory()->create();

        // Create an achievement and associate it with the user
        $lesson = Lesson::factory()->count(3)->create();
        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        $achievement = new FiveLessonsWatched();
        $result = $achievement->qualifier($user);

        // Ensure the BadgeUnlocked event is not fired
        Event::assertNotDispatched(AchievementUnlocked::class);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);
    }
}
