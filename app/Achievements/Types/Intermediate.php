<?php

namespace App\Achievements\Types;

use App\Events\BadgeUnlocked;
use App\Models\Achievement;

class Intermediate extends AchievementsType {

    public string $type = 'badge'; // Type of achievement: comment, lesson, or badge
    public int $value = 4; // Value required to unlock the achievement

    /**
     * Check if the user qualifies for the 'Intermediate' badge achievement.
     *
     * @param mixed $user The user object to check for achievement qualification.
     * @return bool True if the user qualifies for the badge; otherwise, false.
     */
    public function qualifier($user): bool
    {
        // Check if the user's total achievement count matches the required value for the Intermediate badge
        if ($this->getAchievementCount($user) == $this->value) {

            // Fire BadgeUnlocked event for unlocking the badge
            event(new BadgeUnlocked($this->name(), $user));

            return true; // User qualifies for the badge
        }
       
        return false; // User does not qualify for the badge
    }

}