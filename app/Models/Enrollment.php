<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'grade',
    ];

    // Cada inscripcion pertenece a un alumno.
    // belongsTo indica que esta tabla guarda la clave foranea student_id.
    // Esto permite navegar: $enrollment->student.
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Cada inscripcion pertenece a una materia.
    // En este proyecto usamos Subject (no Course).
    // Esto permite navegar: $enrollment->subject.
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
