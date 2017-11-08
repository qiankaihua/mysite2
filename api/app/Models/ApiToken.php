<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $table = 'apitoken';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
