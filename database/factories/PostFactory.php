<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'title' => fake()->text(),
            'content' => fake()->paragraph(),
            'image' => fake()->image(),
            'likes' => fake()->numberBetween(0,1000),
            'is_published' => 1,
            'publish_date' => fake()->dateTimeThisDecade(),
            'user_id' => fake()->numberBetween(1,5),
        ];
    }

}
