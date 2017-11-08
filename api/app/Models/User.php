<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'user';

    public function setPasswordAttribute($pwd) {
        $this->attributes['password'] = app('hash')->make($pwd);
    }

    public function apitokens() {
        return $this->hasMany('App\Models\ApiToken', 'user_id', 'id');
    }
    public function blogs() {
        return $this->hasMany('App\Models\Blog', 'user_id', 'id');
    }
    public function albums() {
        return $this->hasMany('App\Models\Album', 'user_id', 'id');
    }
    public function comment() {
        return $this->hasMany('App\Models\Comment', 'user_id', 'id');
    }
    public function starBlogs() {
        return $this->belongsToMany('App\Models\Blog', 'user_x_blog', 'user_id', 'blog_id');
    }
    public function photos() {
        return $this->hasMany('App\Models\Photo', 'user_id', 'id');
    }
    public function categorys() {
        return $this->hasMany('App\Models\BlogCategory', 'user_id', 'id');
    }
    public function starComment() {
        return $this->belongsToMany('App\Models\Comment', 'user_x_comment', 'user_id', 'comment_id');
    }
}
