<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Part>
 */
class PartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Part ' . $this->faker->name,
            'price' => $this->faker->randomFloat(2,100,999),
            'yield' => 5000,
            'color' => $this->faker->colorName,
        ];
    }
}
