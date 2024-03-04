<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Assignment;


class Uploader extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'uploader_name',
        'uploader_file',
        'uploader_time',
        'assignment_id',
        'student_id'
    ];

    public function assigment()
    {
        return $this->belongsTo(assignment::class, 'assigment_id');
    }

}
