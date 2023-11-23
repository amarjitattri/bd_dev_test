<?php

namespace App\Achievements\Types;

use App\Models\Achievement;

abstract class AchievementsType {

    protected $model;

    public function __construct()
    {
        $this->model = Achievement::firstOrCreate([
            'name' => $this->name(),
            'type' => $this->type,
            'position' => $this->position,
            'value' => $this->value
        ]);
    }

    public function name()
    {
        if(property_exists($this, 'name')) {
            return $this->name;
        }

        return trim(preg_replace('/[A-Z]/', ' $0', class_basename($this)));
    }
    
    public function modelKey()
    {
        return $this->model->getKey();
    }

    public function getAchievementCount($user)
    {
       $userAchievements = $user->achievements->groupBy('type');
       return ( isset($userAchievements['lesson']) ? $userAchievements['lesson']->count() : 0 ) + ( isset($userAchievements['comment']) ? $userAchievements['comment']->count() : 0 );
    }

    abstract public function qualifier($user);
}