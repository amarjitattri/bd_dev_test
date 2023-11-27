<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Achievement extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'value'];   

    /**
     * Define the relationship between achievements and users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    
    /**
     * Unlock the achievement for a specific user.
     *
     * @param \App\Models\User $user The user for whom the achievement will be unlocked.
     * @return void
     */
    public function unlockAchievement(User $user): void
    {
        $this->users()->attach($user);
    }

    /**
     * Get details of achievements for a specific user.
     *
     * @param mixed $query The query builder instance.
     * @param \App\Models\User $user The user for whom achievements will be retrieved.
     * @return array An array containing various achievement details.
     */
    public function scopeGetAchievements($query, User $user): array
    {
        $achievements = $query->orderBy('id')->get();
        $userAchievementsGroupBy = $user->achievements->groupBy('type');
        
        // Collect unlocked achievements based on type
        $unlockedAchievements = $user->achievements->whereIn('type', ['lesson', 'comment'])->pluck('name')->toArray();

        // Get next available achievements and badges
        $nextAchievements = $this->nextAvailableAchievements($userAchievementsGroupBy, $achievements);
        $nextBadge = $this->nextBadge($userAchievementsGroupBy, $achievements);
        $nextBadge = $nextBadge ? $nextBadge->pluck('name')->toArray() : null;

        // Prepare and return the achievement details
        return [
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAchievements,
            'current_badge' => isset($userAchievementsGroupBy['badge']) ? $userAchievementsGroupBy['badge']->pluck('name')->toArray() : null,
            'next_badge' => $nextBadge,
            'remaing_to_unlock_next_badge' => isset($nextBadge) ? count($nextBadge) : null
        ];
    }

    /**
     * Retrieve next available achievements for a user.
     *
     * @param \Illuminate\Support\Collection $userAchievementsGroupBy The achievements grouped by type for the user.
     * @param mixed $achievements The achievements.
     * @return array An array of next available achievements.
     */
    private function nextAvailableAchievements(Collection $userAchievementsGroupBy, $achievements): array
    {
        $nextAchievements = collect(); // Initialize a collection

        // Define achievement types
        $achievementTypes = ['lesson', 'comment'];

        foreach ($achievementTypes as $type) {
            if (isset($userAchievementsGroupBy[$type])) {
                $lastAchievementValue = $userAchievementsGroupBy[$type]->last()->value;

                // Retrieve next available achievements by type and ID comparison
                $nextAchievement = $achievements->where('value', '>', $lastAchievementValue)
                    ->where('type', '=', $type);

                // Merge retrieved achievements into the collection
                $nextAchievements->push($nextAchievement);
            }
        }

        //check if comment achievement exists if not add in next achievements
        if (!$userAchievementsGroupBy->keys()->contains('comment')) {

            // Retrieve next available achievements by type and ID comparison
            $nextAchievement = $achievements->where('type', '=', 'comment');

            // Merge retrieved achievements into the collection
            $nextAchievements->push($nextAchievement);
        }
        
        //check if lesson achievement exists if not add in next achievements
        if (!$userAchievementsGroupBy->keys()->contains('lesson')) {
            // Retrieve next available achievements by type and ID comparison
            $nextAchievement = $achievements->where('type', '=', 'lesson');

            // Merge retrieved achievements into the collection
            $nextAchievements->push($nextAchievement);
        }


        return $nextAchievements->flatten(1)->pluck('name')->toArray(); // Convert the collection to an array
    }

    /**
     * Retrieve the next available badge for a user.
     *
     * @param \Illuminate\Support\Collection $userAchievementsGroupBy The achievements grouped by type for the user.
     * @param \Illuminate\Support\Collection $achievements The achievements collection.
     * @return \Illuminate\Support\Collection|bool The next available badge or false if none exists.
     */
    private function nextBadge(Collection $userAchievementsGroupBy, Collection $achievements): Collection|bool
    {
        // Check if the 'badge' key exists in $userAchievementsGroupBy using null coalescing operator (??)
        $badge = $userAchievementsGroupBy['badge'] ?? null;

        if ($badge !== null) {
            // Get the ID of the last badge using the null-safe operator (->)
            $nextBadgesId = $badge->last()?->id;

            // Return the collection of badges where conditions are met
            return $achievements->where('id', '>', $nextBadgesId)
                ->where('type', 'badge');
        }

        // Return false if no 'badge' type exists
        return false;
    }
    
}
