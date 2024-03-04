<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'title_quiz',
        'description_quiz',
        'open_quiz',
        'close_quiz',
        'time_limit',
        'attempts_allowed',
        'activity_id'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
