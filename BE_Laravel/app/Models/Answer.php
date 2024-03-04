<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'score',
        'answer_text',
        'question_id',
        'quiz_attempt_id',
        'choice_options_id'
    ];
}
