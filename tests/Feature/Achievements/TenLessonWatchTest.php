<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\TenLessonsWatched;
use App\Events\AchievementUnlocked;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TenLessonWatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_watch_ten_lesson()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();

        // Create an achievement and associate it with the user
        $lesson = Lesson::factory()->count(10)->create();
        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        // Create an event and trigger the event listener
        $tenLessonWatch = new TenLessonsWatched();
        $result = $tenLessonWatch->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($tenLessonWatch, $user) {
            return $event->achievement_name === $tenLessonWatch->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_has_not_watch_ten_lesson()
    {
        Event::fake(AchievementUnlocked::class);

        // Create a user
        $user = User::factory()->create();

        // Create an achievement and associate it with the user
        $lesson = Lesson::factory()->count(8)->create();
        $user->watched()->attach($lesson->pluck('id'), ['watched' => true]);

        $achievement = new TenLessonsWatched();
        $result = $achievement->qualifier($user);

        // Ensure the AchievementUnlocked event is not fired
        Event::assertNotDispatched(AchievementUnlocked::class);

       // Assert that the user does not qualify for the Achievement
        $this->assertFalse($result);
    }
}
