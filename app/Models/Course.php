<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'lecturer_id',
    ];

    /**
     * RELATIONSHIP: belongsTo(Lecturer)
     *
     * Many courses can be assigned to the same lecturer, so from the
     * course's side this is a belongsTo. The foreign key lives on the
     * courses table (lecturer_id).
     *
     * Usage: $course->lecturer -> the lecturer responsible for this course.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * RELATIONSHIP: belongsToMany(Student)
     *
     * The other side of the many-to-many: a course contains many
     * students, joined through course_registrations.
     *
     * Usage: $course->students -> every student registered for this course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_registrations')
            ->withPivot(['status', 'registered_at'])
            ->withTimestamps();
    }

    /**
     * RELATIONSHIP: hasMany(CourseRegistration)
     *
     * Raw registration records for this course.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }
}
