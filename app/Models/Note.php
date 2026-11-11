<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'content',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
