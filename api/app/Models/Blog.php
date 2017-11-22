<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $table = 'blog';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function comments() {
        return $this->hasMany('App\Models\Comment', 'blog_id', 'id');
    }
    public function tags() {
        return $this->hasMany('App\Models\Tag', 'blog_id', 'id');
    }
    public function starFrom() {
        return $this->belongsToMany('App\Models\User', 'user_x_blog', 'blog_id', 'user_id');
    }
    public function category() {
        return $this->belongsTo('App\Models\BlogCategory', 'category_id', 'id');
    }
}
