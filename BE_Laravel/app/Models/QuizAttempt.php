<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAttempt extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'total_score',
        'attampt_date',
        'student_id'
    ];
}
