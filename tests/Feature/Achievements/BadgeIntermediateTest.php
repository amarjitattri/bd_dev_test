<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\Intermediate;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeIntermediateTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_has_four_achievements()
    {
        Event::fake(BadgeUnlocked::class);

        // Create a user with four achievements (meeting the Intermediate badge requirement)
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(4)->create(['type' => 'lesson']);
        $user->achievements()->attach($achievements->pluck('id')->toArray());

        $intermediateBadge = new Intermediate();
        $result = $intermediateBadge->qualifier($user);

        // Ensure the BadgeUnlocked event is fired when the user qualifies
        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($intermediateBadge, $user) {
            return $event->badge_name === $intermediateBadge->name() && $event->user === $user;
        });

        // Assert that the user qualifies for the badge
        $this->assertTrue($result);
    }

    public function test_qualifier_returns_false_when_user_has_less_than_four_achievements()
    {
        Event::fake(BadgeUnlocked::class);

        // Create a user with nine achievements (not meeting the Intermediate badge requirement)
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(2)->create(['type' => 'lesson']);
        $user->achievements()->attach($achievements->pluck('id')->toArray());

        $achievement = new Intermediate();
        $result = $achievement->qualifier($user);

        // Ensure the BadgeUnlocked event is not fired
        Event::assertNotDispatched(BadgeUnlocked::class);

        // Assert that the user does not qualify for the badge
        $this->assertFalse($result);
    }
}
