<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_name',
        'course_id',
        'after_video',
        'timer',
    ];

    public function course() : BelongsTo {
        return $this->belongsTo(Course::class);
    }

    public function questions() : HasMany {
        return $this->hasMany(Question::class);
    }

}
