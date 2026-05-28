<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $classrooms = Classroom::factory()->count(5)->create();

        $students = Student::factory()->count(40)->make()->each(function ($student) use ($classrooms) {
            $student->classroom_id = $classrooms->random()->id;
            $student->save();
        });

        $subjects = Subject::factory()->count(6)->create();

        foreach ($students as $student) {
            $selectedSubjects = $subjects->random(3);

            foreach ($selectedSubjects as $subject) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'grade' => mt_rand(600, 1000) / 100,
                ]);
            }
        }

        User::updateOrCreate(
            ['email' => 'admin@escuela.test'],
            [
                'name' => 'Admin Escuela',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
