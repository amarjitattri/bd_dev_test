<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Achievements\Types;

class AchievementServiceProvider extends ServiceProvider
{
    protected $achievements = [
        Types\FirstLessonWatched::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('achievements', function() {
            return collect($this->achievements)->map(function($achievement) {
                return new $achievement;
            });
        });
    }
}
