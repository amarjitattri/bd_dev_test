<?php

namespace App\Achievements\Types;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;

class TwentyCommentsWritten extends AchievementsType {

    public string $name = '20 Comment Written'; // Name of the achievement
    public string $type = 'comment'; // Type of achievement: comment or lesson
    public int $value = 20; // Value required to unlock the achievement

    /**
     * Check if the user qualifies for the '20 Comment Written' achievement.
     *
     * @param mixed $user The user object to check for achievement qualification.
     * @return bool True if the user qualifies for the achievement; otherwise, false.
     */
    public function qualifier($user): bool
    {
        // Check if the user has written exactly 20 comments
        if (isset($user->comments) && $user->comments->count() == $this->value) {

            // Fire AchievementUnlocked event for unlocking the achievement
            event(new AchievementUnlocked($this->name(), $user));
            
            return true; // User qualifies for the achievement
        }
        
        return false; // User does not qualify for the achievement
    }

}