<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\TwentyFiveLessonsWatched;
use App\Events\AchievementUnlocked;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TwentyFiveLessonWatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_watch_twenty_five_lesson()
    {
        Event::fake(AchievementUnlocked::class);

        // Create a user for testing
        $user = User::factory()->create();

        // Create an Lesson and associate it with the user
        $lesson = Lesson::factory()->count(25)->create();
        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        // Create an event and trigger the event listener
        $twentyFiveLessonWatched = new TwentyFiveLessonsWatched();
        $result = $twentyFiveLessonWatched->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($twentyFiveLessonWatched, $user) {
            return $event->achievement_name === $twentyFiveLessonWatched->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_has_not_watch_twenty_five_lesson()
    {
        Event::fake(AchievementUnlocked::class);

        // Create a user with nine achievements (not meeting the Master badge requirement)
        $user = User::factory()->create();
        $lesson = Lesson::factory()->count(13)->create();
        // Create an achievement and associate it with the user

        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        $achievement = new TwentyFiveLessonsWatched();
        $result = $achievement->qualifier($user);

        // Ensure the BadgeUnlocked event is not fired
        Event::assertNotDispatched(AchievementUnlocked::class);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);
    }
}
