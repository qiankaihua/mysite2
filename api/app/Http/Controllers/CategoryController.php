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
            'offset' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);
        $user = \App\Models\User::find($request->input('user_id'));
        if($user === null) abort(404);
        $category_build = $user->categorys();
        if($request->input('offset', null) !== null) $category_build = $category_build->offset($request->input('offset'));
        if($request->input('limit', null) !== null) $category_build = $category_build->take($request->input('take'));
        $categorys = $category_build->get();
        $count = $user->blogs()->count();
        $response = [
            'count' => $count,
            'user' => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'nickname' => $user->nickname,
            ],
            'category' => [],
        ];
        foreach ($categorys as $key => $category) {
            $category->total = $category->blogs()->count();
            $count -= $category->total;
            $response['category'][$key] = [
                'id' => $category->id,
                'title' => $category->title,
                'total' =>  $category->total,
                'created_at' => $category->created_at->timestamp,
            ];
        }
        return response($response);
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'title' => 'required|string|max:20',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $category = \App\Models\BlogCategory;
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
