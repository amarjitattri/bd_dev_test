<?php

namespace Tests\Unit;

use App\Models\Achievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementsTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test to ensure the created Achievement has all required attributes.
     */
    public function test_it_has_all_attributes(): void
    {
        // Create an Achievement instance using the factory
        $achievement = Achievement::factory()->create();

        // Check if the created Achievement instance has all the required attributes
        $this->assertNotNull($achievement->id);
        $this->assertNotNull($achievement->name);
        $this->assertNotNull($achievement->position);
        $this->assertNotNull($achievement->value);
    }
    
}
