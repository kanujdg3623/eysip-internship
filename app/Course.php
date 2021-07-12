<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $casts = [
        'course_videos' => 'array',
    ];
}
