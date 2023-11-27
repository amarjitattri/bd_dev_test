<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    //use RefreshDatabase;

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

        $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('unlocked_achievements', ["First Lesson Watched"])
                ->where('next_available_achievements',
                    [
                        "5 Lessons Watched",
                        "10 Lessons Watched",
                        "25 Lessons Watched",
                        "50 Lessons Watched",
                        "First Comment Written",
                        "3 Comments Written",
                        "5 Comments Written",
                        "10 Comment Written",
                        "20 Comment Written"
                    ])
                ->where('current_badge',
                [
                    "Beginner",
                ])
                ->where('next_badge',
                [
                    "Intermediate",
                    "Advanced",
                    "Master",
                ])
                ->where('remaing_to_unlock_next_badge',
                3)
        );

    }

}
