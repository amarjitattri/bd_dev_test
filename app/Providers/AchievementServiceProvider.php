<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Achievements\Types;

class AchievementServiceProvider extends ServiceProvider
{
    protected array $achievements = [
        Types\FirstLessonWatched::class,
        Types\FiveLessonsWatched::class,
        Types\TenLessonsWatched::class,
        Types\TwentyFiveLessonsWatched::class,
        Types\FiftyLessonsWatched::class,
        Types\FirstCommentWritten::class,
        Types\ThreeCommentsWritten::class,
        Types\FiveCommentsWritten::class,
        Types\TenCommentsWritten::class,
        Types\TwentyCommentsWritten::class,
    ];

    protected array $badges = [
        Types\Beginner::class,
        Types\Intermediate::class,
        Types\Advanced::class,
        Types\Master::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the achievements as singletons
        $this->app->singleton('achievements', function() {
            return collect($this->achievements)->map(function($achievement) {
                return new $achievement;
            });
        });

        // Register the badges as singletons
        $this->app->singleton('badges', function() {
            return collect($this->badges)->map(function($badge) {
                return new $badge;
            });
        });
    }
}
