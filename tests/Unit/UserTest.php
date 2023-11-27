<?php

namespace Tests\Unit;

use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_belongs_to_many_achievements_relationship()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an achievement
        $achievement = Achievement::factory()->create();

        // Attach the achievement to the user
        $user->achievements()->attach($achievement);

        // Retrieve the associated achievements using the relationship
        $associatedAchievements = $user->achievements;

        // Assert that the associated achievements match the ones attached
        $this->assertInstanceOf(Achievement::class, $associatedAchievements->first());
        $this->assertEquals($achievement->id, $associatedAchievements->first()->id);
    }
    public function test_user_belongs_to_many_watched_lesson_relationship()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a lesson
        $lesson = Lesson::factory()->create();

        // Attach the lesson to the user with 'watched' set to true
        $user->lessons()->attach($lesson, ['watched' => true]);

        // Retrieve the lessons marked as watched using the 'watched' relationship
        $watchedLessons = $user->watched;

        // Assert that the retrieved lessons match the ones attached with 'watched' set to true
        $this->assertInstanceOf(Lesson::class, $watchedLessons->first());
        $this->assertEquals($lesson->id, $watchedLessons->first()->id);
    }
    public function test_user_belongs_to_many_lessons_relationship()
    {
        // Create a user
        $user = User::factory()->create();

        // Create lessons
        $lesson1 = Lesson::factory()->create();
        $lesson2 = Lesson::factory()->create();

        // Attach the lessons to the user
        $user->lessons()->attach([$lesson1->id, $lesson2->id]);

        // Retrieve the associated lessons using the 'lessons' relationship
        $associatedLessons = $user->lessons;

        // Assert that the retrieved lessons match the ones attached
        $this->assertInstanceOf(Lesson::class, $associatedLessons->first());
        $this->assertEquals($lesson1->id, $associatedLessons->first()->id);
        $this->assertEquals($lesson2->id, $associatedLessons->last()->id);
    }
    public function test_user_belongs_has_many_comments_relationship()
    {
        // Create a user
        $user = User::factory()->create();

        // Create comments
        $comment1 = Comment::factory()->create(['user_id' => $user->id]);
        $comment2 = Comment::factory()->create(['user_id' => $user->id]);

        // Retrieve the associated comments using the 'comments' relationship
        $associatedComments = $user->comments;

        // Assert that the retrieved comments match the ones created
        $this->assertInstanceOf(Comment::class, $associatedComments->first());
        $this->assertEquals($comment1->id, $associatedComments->first()->id);
        $this->assertEquals($comment2->id, $associatedComments->last()->id);
    }
}
