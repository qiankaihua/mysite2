<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    public function blog() {
        return $this->belongsTo('App\Models\Blog', 'blog_id', 'id');
    }
}
