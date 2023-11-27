<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UnlockLessonWatchedAchievements
{
    /**
     * Handle the event.
     *
     * @param  LessonWatched  $event
     * @return void
     */
    public function handle(LessonWatched $event): void
    {
        // Call the unlockAchievementsLogic method to handle achievement unlocking for the watched lesson
        $this->unlockAchievementsLogic($event);
    }

    /**
     * Unlock achievements logic for the watched lesson.
     *
     * @param  LessonWatched  $event The LessonWatched event instance
     * @return void
     */
    public function unlockAchievementsLogic(LessonWatched $event) : void
    {
        // Retrieve achievement IDs to unlock for the user based on the event
        $achievmentIdsUnlockForUser = app('achievements')->filter->qualifier($event->user)->map->modelKey();

        // Sync the user's achievements with the IDs to unlock, without detaching any existing ones
        $event->user->achievements()->sync($achievmentIdsUnlockForUser);

        //check if new badge unlocked
        $this->unlockBadgesLogic($event);
    }

    /**
     * Unlock badges logic based on user qualification.
     *
     * @param  LessonWatched  $event The LessonWatched event instance
     * @return void
     */
    public function unlockBadgesLogic(LessonWatched $event): void
    {
        // Retrieve badge IDs to unlock for the user based on the event
        $badgeIdsUnlockForUser = app('badges')->filter->qualifier($event->user)->map->modelKey();
        
        // Sync the user's achievements with the badge IDs to unlock, without detaching any existing ones
        $event->user->achievements()->sync($badgeIdsUnlockForUser, false);
    }
}
