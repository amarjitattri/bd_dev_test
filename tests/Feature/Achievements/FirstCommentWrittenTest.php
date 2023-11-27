<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\FirstCommentWritten;
use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FirstCommentWrittenTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_write_first_comment()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();
        // Create an achievement and associate it with the user
        Comment::factory()->create([
                'user_id' => $user->id
        ]);

        $firstCommentWritten = new FirstCommentWritten();
        $result = $firstCommentWritten->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($firstCommentWritten, $user) {
            return $event->achievement_name === $firstCommentWritten->name() && $event->user === $user;
        });

        // Assert that the user does not qualify for the badge
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_not_write_any_comment()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user with no comments
        $user = User::factory()->create();

        $firstCommentWritten = new FirstCommentWritten();
        $result = $firstCommentWritten->qualifier($user);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);

    }
}
