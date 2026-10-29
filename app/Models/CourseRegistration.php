<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A row in the course_registrations pivot table.
 *
 * We model the pivot explicitly (instead of a silent belongsToMany table)
 * because each registration carries its own information: status and the
 * date the student registered.
 */
class CourseRegistration extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'registered_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    /**
     * RELATIONSHIP: belongsTo(Student)
     *
     * Each registration belongs to exactly one student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * RELATIONSHIP: belongsTo(Course)
     *
     * Each registration belongs to exactly one course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
