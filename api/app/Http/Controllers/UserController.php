<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Register(Request $request) {
//        return $request->input('birthday', null);
        $this->validate($request, [
            'username' => [
                'required',
                'string',
                'unique:user,username',
                'regex:/^[a-zA-Z][a-zA-Z0-9_]{4,19}$/',
            ],
            'password' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9]{40}$/',
            ],
            'nickname' => [
                'nullable',
                'string',
                'max:30',
//                'regex:/^[\x{4e00}-\x{9fa5}]{1,9}$|^[\dA-Za-z_]{1,18}$/u',
            ],
            'gender' => 'nullable|integer|in:1,0',
            'email' => 'required|email|unique:user,email',
            'avatar' => 'nullable|image',
            'phone' => [
                'nullable',
                'string',
                'regex:/^1(3|4|5|7|8)[0-9]{9}$/',
            ],
            'birthday' => 'nullable|string',
            'realname' => [
                'nullable',
                'string',
                'max:30',
//                'regex:/^[\x{4e00}-\x{9fa5}]{1,7}$|^[\dA-Za-z_]{1,30}$/u',
            ],
        ]);
        $user = new \App\Models\User;
        $user->username = $request->input('username');
        $user->password = $request->input('password');
        $user->nickname = $request->input('nickname', $request->input('username'));
        $user->gender = $request->input('gender', 0);
        $user->email = $request->input('email');
        $avatar_name = null;
        if(isset($request['avatar'])) {
            $extension = $request->file('avatar')->extension();
            $avatar_name = $request->file('avatar')->move('public/avatar', Uuid::uuid4()->toString().'.'.$extension);
        }
        $user->avatar = ($avatar_name === null) ? null : $avatar_name;
        $user->phone = $request->input('phone', null);
        $user->birthday = $request->input('birthday', null);
        $user->realname = $request->input('realname', null);
        $user->save();
        $response = [
            'success',
        ];
        return $response;
    }
    public function Login(Request $request) {
        $this->validate($request, [
            'username' => [
                'nullable',
                'string',
                'regex:/^[a-zA-Z][a-zA-Z0-9_]{5,19}$/',
            ],
            'password' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9]{40}$/',
            ],
            'email' => 'required_without:username|email',
            'remember' => 'nullable|string|in:true,false',
        ]);
        $user = null;
        if(isset($request['username'])) $user = \App\Models\User::where('username', '=', $request['username'])->first();
        if(isset($request['email']) && !isset($request['username'])) $user = \App\Models\User::where('email', '=', $request['email'])->first();
        if($user === null) abort(404);
        if(!app('hash')->check($request->input('password', ''), $user->password)) abort(401);
        $avatar = null;
        if(isset($user->avatar)) {
            $avatar = str_replace("public/", "", $user->avatar);
            $avatar = str_replace(".", '_', $avatar);
        }
        $apitoken = new \App\Models\ApiToken;
        $apitoken->token = Uuid::uuid4()->toString();
        $apitoken->ip = $request->server('HTTP_X_FORWARDED_FOR', $request->server('REMOTE_ADDR', null));
        if(isset($request['remember']) && $request['remember'] === 'true') $apitoken->expired_at = null;
        else $apitoken->expired_at = Carbon::now()->addMinutes(30);
        $user->apitokens()->save($apitoken);
        return response([
            'token' => $apitoken->token,
            'id' => $user->id,
            'gender' => $user->gender,
            'nickname' => $user->nickname,
            'avatar' => $avatar,
        ]);
    }

    public function Show(Request $request) {
        $this->validate($request, [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
            'want_daletad' => 'nullable|string|in:true,false',
        ]);
        $users_build = \App\Models\User::where('id', '>', 0);
        if ($request->input('want_daleted', 'false') == true) {
            $now_user = $request->input('now_user', null);
            if($now_user === null) abort(401);
            $user_id = $request->input('user_id', 1);
            if($now_user->id !== $user_id && $now_user->id !== 1) abort(403);
            $users_build->withTrashed();
        }
        $Total = count($users_build->get());
        $users_build = $users_build->take($request->input('limit', $Total));
        $users_build = $users_build->offset($request->input('offset', 0));
        $users = $users_build->get();
        $response = [];
        foreach ($users as $key => $user) {
            $avatar = null;
            if(isset($user->avatar)) {
                $avatar = str_replace("public/", "", $user->avatar);
                $avatar = str_replace(".", '_', $avatar);
            }
            $blog_number = count($user->blogs()->get());
            $photo_number = count($user->photos()->get());
            $response[$key] = [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'gender' => $user->gender,
                'avatar' => $avatar,
                'blog' => $blog_number,
                'photos' => $photo_number,
                'created_at' => $user->created_at->timestamp,
                'deleted_at' => $user->deleted_at === null ? null : strtotime($user->deleted_at),
            ];
        }
        return response($response)->header('X-Total', $Total);
    }
    public function ShowDetail(Request $request, $user_id) {
        $now_user = $request->input('now_user', null);
        $user = \App\Models\User::withTrashed()->where('id', '=', $user_id)->first();
        $avatar = null;
        if(isset($user->avatar)) {
            $avatar = str_replace("public/", "", $user->avatar);
            $avatar = str_replace(".", '_', $avatar);
        }
        if($now_user !== null && ($now_user->id === 1 || $now_user->id == $user_id)) {
            return response([
                'id' => $user->id,
                'username' => $user->username,
                'nickname' => $user->nickname,
                'gender' => $user->gender,
                'email' => $user->email,
                'avatar' => $avatar,
                'phone' => $user->phone,
                'birthday' => $user->birthday === null ? null : strtotime($user->birthday),
                'realname' => $user->realname,
                'deleted_at' => $user->deleted_at === null ? null : strtotime($user->deleted_at),
                'created_at' => $user->created_at->timestamp,
            ]);
        }
        else {
            return response([
                'nickname' => $user->nickname,
                'avatar' => $avatar,
                'gender' => $user->gender,
            ]);
        }
    }
    public function Delete(Request $request, $user_id) {
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        if($now_user->id !== 1) abort(403);
        if($user_id == 1) abort(444);
        $user = \App\Models\User::withTrashed()->where('id', '=', $user_id)->first();
        if($user->deleted_at === null) $user->delete();
        return response([
            'success'
        ]);
    }
    public function Change(Request $request, $user_id) {
        $now_user = $request->input('now_user', null);
        if($now_user->id !== 1 && $now_user->id != $user_id) abort(403);
        $this->validate($request, [
            'nickname' => [
                'nullable',
                'string',
                'regex:/^[\x{4e00}-\x{9fa5}]{1,9}$|^[\dA-Za-z_]{1,18}$/u',
            ],
            'gender' => 'nullable|integer|in:0,1',
            'avatar' => 'nullable|image',
            'phone' => [
                'nullable',
                'string',
                'regex:/^1(3|4|5|7|8)[0-9]{9}$/',
            ],
            'birthday' => 'nullable|integer',
            'realname' => [
                'nullable',
                'string',
                'regex:/^[\x{4e00}-\x{9fa5}]{1,7}$|^[\dA-Za-z_]{1,14}$/u',
            ],
        ]);
        $user = \App\Models\User::withTrashed()->where('id', '=', $user_id)->first();
        if($user === null) abort(404);
        if($request->input('nickname', null) !== null) $user->nickname = $request->input('nickname');
        if($request->input('gender', null) !== null) $user->gender = $request->input('gender');
        if($request->input('phone', null) !== null) $user->phone = $request->input('phone');
        if($request->input('birthday', null) !== null) $user->birthday = $request->input('birthday');
        if($request->input('realname', null) !== null) $user->realname = $request->input('realname');
        $avatar_name = null;
        if(isset($request['avatar'])) {
            if($user->avatar !== null) {
                Storage::delete($user->avatar);
            }
            $extension = $request->file('avatar')->extension();
            $avatar_name = $request->file('avatar')->move('public/avatar', Uuid::uuid4()->toString().'.'.$extension);
        }
        if($avatar_name !== null) $user->avatar = $avatar_name;
        $user->save();
        return response([
            'success',
        ]);
    }
    public function Restore(Request $request, $user_id) {
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        if($now_user->id !== 1) abort(403);
        $user = \App\Models\User::withTrashed()->where('id', '=', $user_id)->first();
        if($user->deleted_at !== null) $user->restore();
        return response([
            'success'
        ]);
    }
    public function ChangePassword(Request $request, $user_id) {
        $this->validate($request, [
            'password_old' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9]{40}$/',
            ],
            'password_new' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9]{40}$/',
            ],
        ]);
        $now_user = $request->input('now_user', null);
        if($now_user === null) abort(401);
        if($now_user->id !== 1 && $now_user->id != $user_id) abort(403);
        $user = \App\Models\User::withTrashed('id', '=', $user_id)->first();
//        if(!app('hash')->check($request->input('password', ''), $user->password)) abort(401);
        if(! app('hash')->check($request->input('password_old', ''), $user->password)) abort(422);
        $user->password = $request->input('password_new');
        $user->save();
        return response([
            'success',
        ]);
    }
}
