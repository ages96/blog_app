<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence, // Ensure this line exists
            'content' => $this->faker->paragraph,
            'image' => null, // or specify a valid image path
            'user_id' => User::factory(), // Automatically create a user for the post
        ];
    }
}
