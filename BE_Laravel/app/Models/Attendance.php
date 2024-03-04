<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $timestamps = false;
    protected $fillable = [
        'kategory',
        'info',
        'time_arrive',
        'time_leave',
        'student_id',
        'course_id',
    ];

}
