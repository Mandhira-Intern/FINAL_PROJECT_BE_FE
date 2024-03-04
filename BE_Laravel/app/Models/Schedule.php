<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'day',
        'start_time',
        'end_time',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
