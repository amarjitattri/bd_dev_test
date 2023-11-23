<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UnlockLessonWatchedAchievements
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LessonWatched  $event
     * @return void
     */
    public function handle(LessonWatched $event)
    {
        $this->unlockAchievementsLogic($event);
    }

    /**
     * unlock achievements logic for lesson watched
     */
    public function unlockAchievementsLogic($event) 
    {
        $achievmentIdsUnlockForUser = app('achievements')->filter(function($achievements) use ($event) {
            return $achievements->qualifier($event->user);
        })->map(function($achievements) {
            return $achievements->modelKey();
        });
        
        $event->user->achievements()->sync($achievmentIdsUnlockForUser, false);
    }
}
