<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\FiveCommentsWritten;
use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FiveCommentsWrittenTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_write_first_three_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();

        // Create an achievement and associate it with the user
        Comment::factory()->count(5)->create([
                'user_id' => $user->id
        ]);

        $fiveCommentWritten = new FiveCommentsWritten();
        $result = $fiveCommentWritten->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($fiveCommentWritten, $user) {
            return $event->achievement_name === $fiveCommentWritten->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_not_write_three_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();
        Comment::factory()->count(4)->create([
            'user_id' => $user->id
        ]);

        $fiveCommentWritten = new FiveCommentsWritten();
        $result = $fiveCommentWritten->qualifier($user);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);

    }
}
