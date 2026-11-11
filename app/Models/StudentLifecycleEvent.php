<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLifecycleEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'event_type',
        'previous_program',
        'new_program',
        'previous_status',
        'new_status',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
