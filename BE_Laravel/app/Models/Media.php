<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'name_media',
        'type_media',
        'size_media',
        'file',
        'activity_id'

    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
