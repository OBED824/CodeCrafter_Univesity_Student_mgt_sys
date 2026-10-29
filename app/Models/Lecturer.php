<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecturer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'staff_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department',
        'user_id',
    ];

    /**
     * RELATIONSHIP: hasMany(Course)
     *
     * One lecturer is assigned many courses (each course row stores
     * a lecturer_id pointing back here).
     *
     * Usage: $lecturer->courses -> all courses assigned to this lecturer.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * RELATIONSHIP: belongsTo(User)
     *
     * Links the lecturer profile to a login account so the lecturer
     * dashboard can show only their own courses.
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
