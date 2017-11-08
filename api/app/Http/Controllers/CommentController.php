<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request, $blog_id) {
        $this->validate($request, [
            'offset' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);
        $blog = \App\Models\Blog::find($blog_id);
        if($blog === null) abort(404);
        $comment_build = $blog->comments()->where('comment_id', '=', '0');
        if($request->input('offset', null) !== null) $comment_build = $comment_build->offset($request->input('offset', 0));
        if($request->input('limit', null) !== null) $comment_build = $comment_build->limit($request->input('limit', 0));
        $comments = $comment_build->get();
        $response = [];
        foreach ($comments as $key => $comment) {
            $user = $comment->user()->first();
            $avatar = null;
            if(isset($user->avatar)) {
                $avatar = str_replace("public/", "", $user->avatar);
                $avatar = str_replace(".", '_', $avatar);
            }
            $count = $comment->replyFrom()->count();
            $response[$key] = [
                'id' => $comment->id,
                'user' => ($user === null) ? null : [
                    'id' => $user->id,
                    'avatar' => $avatar,
                    'nickname' => $user->nickname,
                ],
                'star' => $comment->star,
                'count' => $count,
                'contents' => $comment->contents,
                'created_at' => $comment->created_at->timestamp,
            ];
        }
        return response($request);
    }
    public function ShowReply(Request $request, $comment_id) {
        $this->validate($request, [
            'offset' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);
        $comment = \App\Models\Comment::find($comment_id);
        if($comment === null) abort(404);
        $comment_build = $comment->replyFrom();
        if($request->input('offset', null) !== null) $comment_build = $comment_build->offset($request->input('offset', 0));
        if($request->input('limit', null) !== null) $comment_build = $comment_build->limit($request->input('limit', 0));
        $comments = $comment_build->get();
        $response = [];
        foreach ($comments as $key => $com) {
            $user = $com->user()->first();
            $avatar = null;
            if(isset($user->avatar)) {
                $avatar = str_replace("public/", "", $user->avatar);
                $avatar = str_replace(".", '_', $avatar);
            }
            $response[$key] = [
                'id' => $com->id,
                'user' => ($user === null) ? null : [
                    'id' => $user->id,
                    'avatar' => $avatar,
                    'nickname' => $user->nickname,
                ],
                'star' => $com->star,
                'contents' => $com->contents,
                'created_at' => $com->created_at->timestamp,
            ];
        }
        return response($response);
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'blog_id' => 'required|integer|min:1',
            'contents' => 'required|string',
            'comment_id' => 'nullable|integer|min:1',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $blog = \App\Models\Blog::find($request->input('blog_id'));
        if($blog === null) abort(404);
        if($request->input('comment_id', null) !== null) {
            $reply_comment = \App\Models\Comment::find($request->input('comment_id'));
            if ($reply_comment === null) abort(404);
            if ($reply_comment->comment_id !== null) abort(400);
        }
        $comment = new \App\Models\Comment;
        $comment->blog_id = $blog->id;
        $comment->contents = clean($request->input('contents'));
        $comment->star = 0;
        $comment->comment_id = $request->input('comment_id', 0);
        $user->comment()->save($comment);
        return response([
            'success',
        ]);
    }
    private function DeleteReply(\App\Models\Comment $comment) {
        if($comment === null) return ;
        $comments = $comment->replyFrom();
        foreach ($comments as $com) {
            $this->DeleteReply($com);
        }
        $comment->delete();
        return ;
    }
    public function Delete(Request $request, $comment_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $comment = \App\Models\Comment::find('id', '=', $comment_id);
        if($comment === null) abort(404);
        $blog = $comment->blog()->first();
        if($blog === null) abort(404);
        if($user->id !== $blog->user_id && $user->id !== $comment->user_id && $user->id !== 1) abort(403);
        $this->DeleteReply($comment);
        return response([
            'success',
        ]);
    }
    public function Star(Request $request, $comment_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $comment = \App\Models\Comment::find('id', '=', $comment_id);
        if($comment === null) abort(404);
        $user->starComments()->toggle($comment_id);
        $comment->star = $comment->starFrom()->count();
        $comment->save();
        return response([
            'success',
        ]);
    }
}
