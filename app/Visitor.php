<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    //
    protected $casts = [
        'vis_session_details' => 'array',
    ];
}
