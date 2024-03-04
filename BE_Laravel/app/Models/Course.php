<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'course_name',
        'capacity',
        'semester',
        'lecture_id',
        'studyProgram_id',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function enrollments()
    {
        return $this->hasMany(EnrollCourse::class, 'course_id');
    }
}
