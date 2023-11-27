<?php

namespace App\Achievements\Types;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;

class TwentyFiveLessonsWatched extends AchievementsType {

    public string $name = '25 Lessons Watched'; // Name of the achievement
    public string $type = 'lesson'; // Type of achievement: comment or lesson
    public int $value = 25; // Value required to unlock the achievement

    /**
     * Check if the user qualifies for the '25 Lessons Watched' achievement.
     *
     * @param mixed $user The user object to check for achievement qualification.
     * @return bool True if the user qualifies for the achievement; otherwise, false.
     */
    public function qualifier($user): bool
    {
        // Check if the user has watched exactly 25 lessons
        if (isset($user->lessons) && $user->lessons->count() == $this->value) {

            //fired achievement unlocked event
            event(new AchievementUnlocked($this->name(), $user));

            return true; // User qualifies for the achievement
        }
        return false; // User does not qualify for the achievement
    }

}