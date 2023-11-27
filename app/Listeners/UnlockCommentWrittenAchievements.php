<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UnlockCommentWrittenAchievements
{
    /**
     * Handle the event.
     *
     * @param  CommentWritten  $event
     * @return void
     */
    public function handle(CommentWritten $event): void
    {
        $this->unlockAchievementsLogic($event);
    }

    /**
     * Unlock achievements logic for the written comment.
     *
     * @param  CommentWritten  $event The CommentWritten event instance
     * @return void
     */
    public function unlockAchievementsLogic(CommentWritten $event): void
    {
        // Retrieve achievement IDs to unlock for the user who wrote the comment
        $achievmentIdsUnlockForUser = app('achievements')->filter->qualifier($event->comment->user)->map->modelKey();
        
        // Sync the user's achievements with the IDs to unlock, without detaching any existing ones
        $event->comment->user->achievements()->sync($achievmentIdsUnlockForUser, false);

        //check if new badge unlocked
        $this->unlockBadgesLogic($event);
    }

    /**
     * Unlock badges logic based on user qualification.
     *
     * @param  CommentWritten  $event The CommentWritten event instance
     * @return void
     */
    public function unlockBadgesLogic(CommentWritten $event): void
    {
        // Retrieve badge IDs to unlock for the user who wrote the comment
        $badgeIdsUnlockForUser = app('badges')->filter->qualifier($event->comment->user)->map->modelKey();

        // Sync the user's achievements with the badge IDs to unlock, without detaching any existing ones
        $event->comment->user->achievements()->sync($badgeIdsUnlockForUser, false);
    }
}
