<?php

namespace App\Achievements\Types;

use App\Models\Achievement;

abstract class AchievementsType {

    protected Achievement $model; // Typed property declaration for $model

    /**
     * Constructor initializes the Achievement model.
     */
    public function __construct()
    {
        // Using firstOrCreate to create or retrieve an Achievement instance based on certain attributes
        $this->model = Achievement::firstOrCreate([
            'name' => $this->name(),
            'type' => $this->type ?? null,
            'value' => $this->value ?? null
        ]);
    }

    /**
     * Get the name of the achievement type.
     */
    public function name(): string
    {
        if(property_exists($this, 'name')) {
            return $this->name;
        }

        return trim(preg_replace('/[A-Z]/', ' $0', class_basename($this)));
    }
    
    /**
     * Get the key of the Achievement model.
     */
    public function modelKey(): ?int
    {
        return $this->model->getKey();
    }

    /**
     * Get the count of achievements for a specific user.
     *
     * @param mixed $user The user for whom achievements will be counted.
     * @return int The count of achievements for the user.
     */
    public function getAchievementCount($user): int
    {
       $userAchievements = $user->achievements->groupBy('type');
       return ( isset($userAchievements['lesson']) ? $userAchievements['lesson']->count() : 0 ) + ( isset($userAchievements['comment']) ? $userAchievements['comment']->count() : 0 );
    }

    /**
     * Qualify a user for the achievement.
     *
     * @param mixed $user The user to qualify for the achievement.
     * @return mixed Implementation-specific qualification result for the user.
     */
    abstract public function qualifier($user); // Method signature for qualifying a user
}