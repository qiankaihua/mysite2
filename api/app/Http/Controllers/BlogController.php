<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Store(Request $request) {
        $this->validate($request, [
            'category_id' => 'nullable|integer',
            'title' => 'required|string|min:1|max:100',
            'contents' => 'required|string|min:1',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $blog = new \App\Models\Blog;
        $clean_contents = $request->input('contents', null);
        $clean_title = $request->input('title', null);
        $blog->title = $clean_title;
        $blog->contents = $clean_contents;
        $blog->category_id = $request->input('category_id', 0);
        $blog->star = 0;
        $user->blogs()->save($blog);
        return response([
            'id' => $blog->id,
            'contents' => $request->input('contents', null),
        ]);
    }
    public function Delete(Request $request, $blog_id) {
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        $blog = \App\Models\Blog::withTrashed()->where('id', '=', $blog_id)->first();
        if($blog === null) abort(404);
        if($now_user->id !== $blog->user_id && $now_user->id !== 1) abort(403);
        if($blog->deleted_at === null) $blog->delete();
        return response([
            'success',
        ]);
    }
    public function Restore(Request $request, $blog_id) {
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        $blog = \App\Models\Blog::withTrashed()->where('id', '=', $blog_id)->first();
        if($blog === null) abort(404);
        if($now_user->id !== $blog->user_id && $now_user->id !== 1) abort(403);
        if($blog->deleted_at !== null) $blog->restore();
        return response([
            'success',
        ]);
    }
    public function Change(Request $request, $blog_id) {
        $this->validate($request, [
            'category_id' => 'nullable|integer|min:0',
            'title' => 'nullable|string|min:1|max:100',
            'contents' => 'nullable|string|min:1',
        ]);
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        $blog = \App\Models\Blog::withTrashed()->find($blog_id);
        if($blog === null) abort(404);
        if($now_user->id !== $blog->user_id && $now_user->id !== 1) abort(403);
        if($request->input('category_id', null) !== null) $blog->category_id = $request->input('category_id');
        if($request->input('title', null) !== null) $blog->title = $request->input('title');
        if($request->input('contents', null) !== null) $blog->contents = $request->input('contents');
        $blog->save();
        return response([
            'success',
        ]);
    }
    public function Show(Request $request) {
        $this->validate($request, [
            'category_id' => 'nullable|integer|min:0',
            'user_id' => 'nullable|integer|min:1',
            'want_deleted' => 'nullable|string|in:true,false',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);
        if($request->input('want_deleted', 'false') === 'true') {
            $now_user = $request->input('now_user', null);
            if($now_user === null) abort(401);
            $user_id = +$request->input('user_id', 1);
            if($now_user->id !== $user_id && $now_user->id !== 1) abort(403);
            $blog_build = \App\Models\Blog::withTrashed()->where('id', '>', 0);
        }
        else $blog_build = \App\Models\Blog::where('id', '>', '0');
        if($request->input('user_id', null) !== null) $blog_build = $blog_build->where('user_id', '=', $request->input('user_id'));
        $Total = count($blog_build->get());
        if($request->input('category_id', null) !== null) $blog_build = $blog_build->where('category_id', '=', $request->input('category_id'));
        $seleted = count($blog_build->get());
        if($request->input('offset', null) !== null) $blog_build = $blog_build->offset($request->input('offset', 0));
        if($request->input('limit', null) !== null) $blog_build = $blog_build->take($request->input('limit', 0));
        $blogs = $blog_build->get();
        $response = [];
        foreach ($blogs as $key => $blog) {
            $user = $blog->user()->first();
            $category = $blog->category()->first();
            $response_tags = [];
            $tags = $blog->tags()->get();
            $avatar = null;
            if(isset($user->avatar)) {
                $avatar = str_replace("public/", "", $user->avatar);
                $avatar = str_replace(".", '_', $avatar);
            }
            foreach ($tags as $tag) {
                array_push($response_tags, $tag->value);
            }
            $response[$key] = [
                'id' => $blog->id,
                'user' => ($user === null) ? null : [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    'avatar' => $avatar,
                ],
                'title' => $blog->title,
                'category' => ($category === null) ? null : [
                    'id' => $category->id,
                    'title' => $category->title,
                ],
                'star' => $blog->star,
                'tag' => $response_tags,
                'created_at' => $blog->created_at->timestamp,
                'updated_at' => $blog->updated_at->timestamp,
                'deleted_at' => $blog->deleted_at === null ? null : strtotime($blog->deleted_at),
            ];
        }
        return response($response)->header('X-Total', $Total)->header('seleted', $seleted);
    }
    public function ShowDetail(Request $request, $blog_id) {
        $blog = \App\Models\Blog::withTrashed()->find($blog_id);
        if($blog === null) abort(404);
        $category = $blog->category()->first();
        $user = $blog->user()->first();
        $tags = $blog->tags()->get();
        $tag_list = [];
        foreach ($tags as $tag) {
            array_push($tag_list, $tag->value);
        }
        $avatar = null;
        if(isset($user->avatar)) {
            $avatar = str_replace("public/", "", $user->avatar);
            $avatar = str_replace(".", '_', $avatar);
        }
        if ($user !== null) {
            $count_blog = count($user->blogs()->get());
            $blogs = $user->blogs()->latest('updated_at')->take(5)->get();
            $blogList = [];
            foreach ($blogs as $b) {
                array_push($blogList, [
                    'id' => $b->id,
                    'title' => $b->title,
                    'star' => $b->star,
                ]);
            }
        }
        $stared = false;
        $nowuser = $request->input('now_user', null);
        if ($nowuser !== null) {
            if ($nowuser->starBlogs()->withTrashed()->find($blog_id) !== null) $stared = true;
        }

        return response([
            'id' => $blog->id,
            'title' => $blog->title,
            'contents' => $blog->contents,
            'star' => $blog->star,
            'tags' => $tag_list,
            'category' => ($category === null) ? null : [
                'id' => $category->id,
                'title' => $category->title,
            ],
            'user' => ($user === null) ? null : [
                'id' => $user->id,
                'avatar' => $avatar,
                'nickname' => $user->nickname,
                'count' => $count_blog,
                'blogs' => $blogList,
            ],
            'stared' => $stared,
            'created_at' => $blog->created_at->timestamp,
            'updated_at' => $blog->updated_at->timestamp,
        ]);
    }
    public function Star(Request $request, $blog_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $blog = \App\Models\Blog::find($blog_id);
        if($blog === null) abort(404);
        $user->starBlogs()->toggle($blog_id);
        $blog->star = $blog->starFrom()->count();
        $blog->save();
        return response([
            'success',
        ]);
    }
}
