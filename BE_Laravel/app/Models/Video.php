<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name_video',
        'description_link',
        'link',
        'activity_id'
    ]; 
}
