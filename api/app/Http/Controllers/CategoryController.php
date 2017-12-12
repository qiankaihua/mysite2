<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request) {
        $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);
        $user = \App\Models\User::find($request->input('user_id'));
        if($user === null) abort(404);
        $category_build = $user->categorys();
        $max = count($category_build->get());
        $category_build = $category_build->offset($request->input('offset', 0));
        $category_build = $category_build->take($request->input('limit', $max));
        $categorys = $category_build->get();
        $count = $user->blogs()->count();
        $nocate = \App\Models\Blog::where('user_id', '=', $request->input('user_id'))->where('category_id', '=', '0')->get();
        $nocate_blog = \App\Models\Blog::where('user_id', '=', $request->input('user_id'))->where('category_id', '=', '0')->latest('updated_at')->take(5)->get();
        $nocate = count($nocate);
        $nocate_blogs = [];
        foreach ($nocate_blog as $b) {
            array_push($nocate_blogs, [
                'title' => $b->title,
                'id' => $b->id,
                ]);
        }
        $response = [
            'count' => $count,
            'nocate' => $nocate,
            'nocate_blog' => $nocate_blogs,
            'user' => [
                'id' => $user->id,
                'nickname' => $user->nickname,
            ],
            'category' => [],
        ];
        foreach ($categorys as $key => $category) {
            $category->total = $category->blogs()->count();
            $blogs = $category->blogs()->latest('updated_at')->take(5)->get();
            $blog = [];
            foreach ($blogs as $b) {
                array_push($blog, [
                    'title' => $b->title,
                    'id' => $b->id,
                ]);
            }
            $response['category'][$key] = [
                'id' => $category->id,
                'title' => $category->title,
                'total' =>  $category->total,
                'blog' => $blog,
                'created_at' => $category->created_at->timestamp,
            ];
        }
        return response($response)->header('X-total', $max);
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'title' => 'required|string|max:20',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $ca = $user->categorys()->where('title', '=', $request->input('title'))->get();
        if (count($ca) !== 0) abort(444);
        $category = new \App\Models\BlogCategory;
        $category->total = 0;
        $category->title = clean($request->input('title'));
        $user->categorys()->save($category);
        return response([
            'success',
        ]);
    }
    public function Change(Request $request, $category_id) {
        $this->validate($request, [
            'title' => 'required|string|max:20',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $category = \App\Models\BlogCategory::find($category_id);
        if($category === null) abort(404);
        $cates = $user->categorys()->where('title', '=', $request->input('title'))->get();
        foreach ($cates as $ca) {
            if ($ca->id !== +$category_id) abort(444);
        }
        if($user->id !== $category->user_id && $user->id !== 1) abort(403);
        $category->title = clean($request->input('title'));
        $user->categorys()->save($category);
        return response([
            'success',
        ]);
    }
    public function Delete(Request $request, $category_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $category = \App\Models\BlogCategory::find($category_id);
        if($category === null) abort(404);
        if($user->id !== $category->user_id && $user->id !== 1) abort(403);
        $blogs = $category->blogs()->get();
        foreach ($blogs as $blog) {
            $blog->category_id = 0;
            $blog->save();
        }
        $category->delete();
        return response([
            'success',
        ]);
    }
}
