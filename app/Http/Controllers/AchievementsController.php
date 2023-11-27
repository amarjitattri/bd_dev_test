<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    /**
     * Display the achievements for a specific user.
     *
     * @param \App\Models\User $user The user for whom achievements will be retrieved.
     * @return \Illuminate\Http\JsonResponse JSON response containing achievement details.
     */
    public function index(User $user): JsonResponse
    {
        // Get achievements for the user using the scope method
        $getAchievements = Achievement::getAchievements($user);
        
        // Extract necessary details for user response
        $badges = [
            'unlocked_achievements' => $getAchievements['unlocked_achievements'],
            'next_available_achievements' => $getAchievements['next_available_achievements'],
            'current_badge' => $getAchievements['current_badge'],
            'next_badge' => $getAchievements['next_badge'],
            'remaing_to_unlock_next_badge' => $getAchievements['remaing_to_unlock_next_badge']
        ];

        // Return the badges and achievement as a JSON response
        return response()->json($badges);
    }
}
