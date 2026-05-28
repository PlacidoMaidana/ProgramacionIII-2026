<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Algebra',
                'Fisica',
                'Quimica',
                'Historia',
                'Programacion',
                'Biologia',
                'Geografia',
                'Literatura',
            ]),
            'code' => strtoupper($this->faker->unique()->bothify('MAT-####')),
        ];
    }
}
