<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name_assignment',
        'description',
        'file_assignment',
        'type_assignment',
        'allow_submission',
        'due_date',
        'cut_off',
        'remind_grade',
        'max_file',
        'max_size',
        'activity_id'
    ];
}
