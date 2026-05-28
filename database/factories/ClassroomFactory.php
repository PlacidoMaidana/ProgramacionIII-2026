<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Aula ' . $this->faker->unique()->bothify('??-##'),
            'capacity' => $this->faker->numberBetween(20, 45),
        ];
    }
}
