<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';

    public function blog() {
        return $this->belongsTo('App\Models\Blog', 'blog_id', 'id');
    }
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function replyTo() {
        return $this->belongsTo('App\Models\Comment', 'comment_id', 'id');
    }
    public function replyFrom() {
        return $this->hasMany('App\Models\Comment', 'comment_id', 'id');
    }
    public function starFrom() {
        return $this->belongsToMany('App\Models\User', 'user_x_comment', 'comment_id', 'user_id');
    }
}
