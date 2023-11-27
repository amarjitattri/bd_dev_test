<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\TwentyCommentsWritten;
use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TwentyCommentsWrittenTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_write_twenty_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();

        Comment::factory()->count(20)->create([
                'user_id' => $user->id
        ]);

        $twentyCommentsWritten = new TwentyCommentsWritten();
        $result = $twentyCommentsWritten->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($twentyCommentsWritten, $user) {
            return $event->achievement_name === $twentyCommentsWritten->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_not_write_twenty_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();
        Comment::factory()->count(19)->create([
            'user_id' => $user->id
        ]);
        $comments = new TwentyCommentsWritten();
        $result = $comments->qualifier($user);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);

    }
}
