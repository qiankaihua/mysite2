<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class AlbumController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request) {
        $this->validate($request, [
            'user_id' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);
        $user = \App\Models\User::find($request->input('user_id'));
        if($user === null) abort(404);
        $album_build = $user->albums();
        $max = count($album_build->get());
        $album_build = $album_build->offset($request->input('offset', 0));
        $album_build = $album_build->take($request->input('limit', $max));
        $albums = $album_build->get();
        $count = $user->photos()->count();
        $nocate = \App\Models\Photo::where('user_id', '=', $request->input('user_id'))->where('album_id', '=', '0')->get();
        $nocate_photo = \App\Models\Photo::where('user_id', '=', $request->input('user_id'))->where('album_id', '=', '0')->latest('updated_at')->take(5)->get();
        $nocate = count($nocate);
        $nocate_photos = [];
        foreach ($nocate_photo as $p) {
            array_push($nocate_photos, [
                'name' => $p->name,
                'path' => $p->url,
                'id' => $p->id,
            ]);
        }
        $response = [
            'count' => $count,
            'nocate' => $nocate,
            'nocate_photo' => $nocate_photos,
            'user' => [
                'id' => $user->id,
                'nickname' => $user->nickname,
            ],
            'album' => [],
        ];
        foreach ($albums as $key => $album) {
            $album->total = $album->photos()->count();
            $photos = $album->photos()->latest('updated_at')->take(5)->get();
            $photo = [];
            foreach ($photos as $p) {
                array_push($photo, [
                    'name' => $p->name,
                    'path' => $p->url,
                    'id' => $p->id,
                ]);
            }
            $response['album'][$key] = [
                'id' => $album->id,
                'total' =>  $album->total,
                'title' => $album->title,
                'intro' => $album->intro,
                'photo' => $photo,
                'created_at' => $album->created_at,
            ];
        }
        return response($response)->header('X-total', $max);
    }
    public function ShowDetail(Request $request, $album_id) {
        $album = \App\Models\Album::find($album_id);
        if($album === null) abort(404);
        $user = $album->user()->first();
        $avatar = null;
        if(isset($user->avatar)) {
            $avatar = str_replace("public/", "", $user->avatar);
            $avatar = str_replace(".", '_', $avatar);
        }
        return response([
            'id' => $album->id,
            'user' => [
                'id' => $user->id,
                'avatar' => $avatar,
                'nickname' => $user->nickname,
            ],
            'title' => $album->title,
            'intro' => $album->intro,
            'created_at' => $album->created_at->timestamp,
        ]);
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'title' => 'required|string',
            'intro' => 'required|string',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $album = \App\Models\Album;
        $album->title = clean($request->input('title'));
        $album->intro = clean($request->input('intro'));
        $user->albums()->save($album);
        return response([
            'success',
        ]);
    }
    public function Change(Request $request, $album_id) {
        $this->validate($request, [
            'title' => 'nullable|string',
            'intro' => 'nullable|string',
        ]);
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $album = \App\Models\Album::find($album_id);
        if($album === null) abort(404);
        if($album->user_id !== $user->id && $user->id !== 1) abort(403);
        if($request->input('title', null) !== null) $album->title = clean($request->input('title'));
        if($request->input('intro', null) !== null) $album->intro = clean($request->input('intro'));
        $album->save();
        return response([
            'success',
        ]);
    }
    public function Delete(Request $request, $album_id) {
        $user = $request->input('now_user', null);
        if($user === null) abort(401);
        $album = \App\Models\Album::find($album_id);
        if($album === null) abort(404);
        if($album->user_id !== $user->id && $user->id !== 1) abort(403);
        $album->delete();
        return response([
            'success',
        ]);
    }
}
