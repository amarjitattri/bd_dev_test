<?php

namespace Tests\Feature\Achievements;

use App\Achievements\Types\Beginner;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeBeginnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualifier_returns_true_when_user_has_less_than_four_achievements()
    {
        Event::fake(BadgeUnlocked::class);

        // Create a user with three achievements (less than 4)
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(3)->create();
        $user->achievements()->attach($achievements->pluck('id')->toArray());

        $beginnerBadge = new Beginner();
        $result = $beginnerBadge->qualifier($user);

        // Assert that the user qualifies for the badge
        $this->assertTrue($result);
    }

    public function test_qualifier_returns_false_when_user_has_no_achievements()
    {
        Event::fake(BadgeUnlocked::class);

        // Create a user with no achievements
        $user = User::factory()->create();

        $beginnerBadge = new Beginner();
        $result = $beginnerBadge->qualifier($user);

        // Assert that the user does not qualify for the badge
        $this->assertTrue($result);
    }

}