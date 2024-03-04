<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'text_question',
        'type_question',
        'poin_question',
        'quiz_id'
    ];
}
