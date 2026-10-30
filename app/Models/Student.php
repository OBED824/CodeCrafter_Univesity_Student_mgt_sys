<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
 /** @use HasFactory<StudentFactory> */
 use HasFactory;

 /**
     * Fields that can be filled in bulk (e.g. from a validated form).
     *
     * @var list<string>
     */
 protected $fillable = [
 'student_number',
 'first_name',
 'last_name',
 'email',
'phone',
 'gender',
 'date_of_birth',
'program',
 'user_id',
];

/**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
 protected function casts(): array
 {
 return [
 'date_of_birth' => 'date',
];
}

/**
     * RELATIONSHIP: belongsToMany(Course)
     *
     * A student registers for many courses and a course has many students,
     * so this is a many-to-many relationship joined through the
     * course_registrations pivot table.
     *
     * Usage: $student->courses  -> every course this student registered for.
     */
    public function courses(): BelongsToMany {
         return $this->belongsToMany(Course::class, 'course_registrations')
         ->withPivot(['status', 'registered_at'])
         ->withTimestamps();
 }

 /**
     * RELATIONSHIP: hasMany(CourseRegistration)
     *
     * The raw registration records themselves, useful when we need the
     * pivot data (status, registration date) rather than just courses.
     */
 public function registrations(): HasMany
  {
  return $this->hasMany(CourseRegistration::class);
 }

 /**
     * RELATIONSHIP: belongsTo(User)
     *
     * A student profile may be linked to one login account (nullable).
     * This is how the system knows "the logged-in user IS this student"
     * and can show only their own records.
     */
 public function user(): BelongsTo
 {
 return $this->belongsTo(User::class);
 }

 /**
     * Convenience: full name for display in views.
     */
 public function getFullNameAttribute(): string
 {
 return $this->first_name.' '.$this->last_name;
 }
}