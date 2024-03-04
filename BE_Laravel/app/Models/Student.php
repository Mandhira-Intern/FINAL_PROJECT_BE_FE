<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'city',
        'grade',
        'status',
        'user_id',
    ];

    public function enrollments()
    {
        return $this->hasMany(EnrollCourse::class, 'student_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
