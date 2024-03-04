<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'forum_title',
        'description',
        'activity_id'
    ];
    
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
