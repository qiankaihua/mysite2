<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_category';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function blogs() {
        return $this->hasMany('App\Models\Blog', 'category_id', 'id');
    }
}
