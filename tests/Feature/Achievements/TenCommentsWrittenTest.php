<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\TenCommentsWritten;
use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TenCommentsWrittenTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_write_ten_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();
        // Create an achievement and associate it with the user
        Comment::factory()->count(10)->create([
                'user_id' => $user->id
        ]);

        $tenCommentsWritten = new TenCommentsWritten();
        $result = $tenCommentsWritten->qualifier($user);

        // Ensure the AchievementUnlocked event is fired when the user qualifies
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($tenCommentsWritten, $user) {
            return $event->achievement_name === $tenCommentsWritten->name() && $event->user === $user;
        });
        // Assert that the user has the achievement unlocked
        $this->assertTrue($result);

    }
    public function test_qualifier_returns_false_when_user_not_write_ten_comments()
    {
        Event::fake(AchievementUnlocked::class);
        // Create a user for testing
        $user = User::factory()->create();

        Comment::factory()->count(9)->create([
            'user_id' => $user->id
        ]);

        $comments = new TenCommentsWritten();
        $result = $comments->qualifier($user);

        // Assert that the user does not qualify for the Achievement
        $this->assertFalse($result);

    }
}
