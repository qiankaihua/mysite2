<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request) {
        $this->validate($request, [
            'blog_id' => 'required|integer|min:1',
        ]);
        $blog = \App\Models\Blog::find($request->input('blog_id'));
        if($blog === null) abort(404);
        $tags = $blog->tags()->get();
        $response = [];
        foreach ($tags as $key => $tag) {
            $response[$key] = [
                'value' => $tag->value,
            ];
        }
        return response($response);
    }
    public function Store(Request $request) {
        $this->validate($request,[
            'value' => 'required|string|max:20',
            'blog_id' => 'required|integer|min:1',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $blog = \App\Models\Blog::withTrashed()->where('id', '=', $request->input('blog_id'))->first();
        if($blog === null) abort(404);
        if($blog->user_id !== $user->id && $user->id !== 1) abort(403);
        $has_tag =\App\Models\Tag::where('blog_id', '=', $request->input('blog_id'))->where('value', '=', $request->input('value'))->first();
        if (count($has_tag) !== 0) return response([$has_tag->id]); // return response(['success']);
        $tag = new \App\Models\Tag;
        $tag->value = $request->input('value');
        $blog->tags()->save($tag);
        return response([
            'success',
        ]);
    }
    public function Delete(Request $request, $tag_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $tag = \App\Models\Tag::find($tag_id);
        if($tag === null) abort(404);
        $blog = \App\Models\Blog::withTrashed()->where('id', '=', $tag->blog_id)->first();
        if($blog === null) abort(404);
        if($blog->user_id !== $user->id && $user->id !== 1) abort(403);
        $tag->delete();
        return response([
            'success',
        ]);
    }
}
