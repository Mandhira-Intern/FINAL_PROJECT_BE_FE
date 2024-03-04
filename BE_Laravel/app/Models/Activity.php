<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'activity_name',
        'description',
        'learning_media_type',
        'course_id'
    ];

    public function assignment()
    {
        return $this->hasMany(Assignment::class, 'activity_id');
    }

    public function forum()
    {
        return $this->hasMany(Forum::class, 'activity_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'activity_id');
    }

    public function announcement()
    {
        return $this->hasMany(Announcement::class, 'activity_id');
    }

    public function video()
    {
        return $this->hasMany(Video::class, 'activity_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'activity_id');
    }

    

}