<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChoiceOption extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'option_text',
        'is_correct',
        'question_id'
    ];
}
