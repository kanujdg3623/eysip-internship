<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $casts = [
        'vis_session_id' => 'array',
    ];
}
