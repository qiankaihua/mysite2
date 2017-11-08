<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request) {
        $this->validate($request, [
            'user_id' => 'nullable|integer|min:1',
            'Album_id' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
            'type' => 'nullable|string|in:random,list',
        ]);
        $photo_build = \App\Models\Photo::where('id', '>', '0');
        if($request->input('user_id', null) !== null) $photo_build->where('user_id', '=', $request->input('user_id'));
        if($request->input('album_id', null) !== null) $photo_build->where('album_id', '=', $request->input('album_id'));
        if($request->input('offset', null) !== null) $photo_build->offset($request->input('offset'), 0);
        if($request->input('limit', null) !== null) $photo_build->take($request->input('limit', 0));
        if($request->input('type', null) === 'random') $photo_build->inRandomOrder();
        $photos = $photo_build->get();
        $response =[];
        foreach ($photos as $key => $photo) {
            $user = $photo->user()->first();
            $album = $photo->album()->first();
            $avatar = null;
            if(isset($user->avatar)) {
                $avatar = str_replace("public/", "", $user->avatar);
                $avatar = str_replace(".", '_', $avatar);
            }
            $url = null;
            if(isset($photo->url)) {
                $url = str_replace("public/", "", $photo->url);
                $url = str_replace(".", '_', $url);
            }
            $response[$key] = [
                'id' => $photo->id,
                'user' => [
                    'id' => $user->user_id,
                    'nickname' => $user->nickname,
                    'avatar' => $avatar,
                ],
                'album' => [
                    'id' => $album->id,
                    'title' => $album->title,
                    'intro' => $album->intro,
                ],
                'url' => $url,
                'name' => $photo->name,
                'created_at' => $photo->created_at->timestamp,
            ];
        }
        return response($response);
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'photo' => 'required|image',
            'name' => 'required|string|max:30',
            'album_id' => 'nullable|integer|min:1',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $photo = \App\Models\Photo;
        $photo->name = clean($request->input('name'));
        if($request->input('album_id', null) !== null) $photo->album_id = $request->input('album_id');
        $path = $request->file('photo')->store('public/photo');
        $photo->url = $path;
        return response([
            'success',
        ]);
    }
    public function Change(Request $request, $photo_id) {
        $this->validate($request, [
            'name' => 'nullable|string|max:30',
            'album_id' => 'nullable|integer|min:1',
        ]);
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        $photo = \App\Models\Photo::find($photo_id);
        if($photo === null) abort(404);
        if($photo->user_id !== $now_user->id && $now_user->id !== 1) abort(403);
        if($request->input('name', null) !== null) $photo->name = clean($request->input('name'));
        if($request->input('album_id', null) !== null) $photo->album_id = $request->input('album_id');
        return response([
            'success',
        ]);
    }
    public function Delete(Request $request, $photo_id) {
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        $photo = \App\Models\Photo::find($photo_id);
        if($photo === null) abort(404);
        if($photo->user_id !== $now_user->id && $now_user->id !== 1) abort(403);
        $photo->delete();
        return response([
            'success',
        ]);
    }
}
