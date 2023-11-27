<?php

namespace App\Achievements\Types;

use App\Events\BadgeUnlocked;
use App\Models\Achievement;

class Beginner extends AchievementsType {

    public string $type = 'badge'; // Type of achievement: comment or lesson
    public int $value = 0; // Value required to unlock the achievement

    /**
     * Check if the user qualifies for the Beginner badge achievement.
     *
     * @param mixed $user The user object to check for achievement qualification.
     * @return bool True if the user qualifies for the badge; otherwise, false.
     */
    public function qualifier($user): bool
    {
        // Check if the user's achievement count is less than 4 for the Beginner badge
        return $this->getAchievementCount($user) < 4;
    }

}