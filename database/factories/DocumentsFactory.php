<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Documents>
 */
class DocumentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word . '.pdf',
            'key' => 'documents/' . $this->faker->randomNumber() . '/' . $this->faker->word . '.pdf',
            'user_id' => \App\Models\User::factory(),
            'isRecycle' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
