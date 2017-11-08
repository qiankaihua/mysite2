<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class AddTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->input('token', null);
        if($token === null) {
            $token_head = $request->header('token');
            if($token_head === null) return $next($request);
            $token = $token_head;
        }
        $apiToken = \App\Models\ApiToken::where('token', '=', $token)->first();
        if($apiToken === null) return $next($request);
        $user = \App\Models\User::withTrashed()->where('id', '=', $apiToken->user_id)->first();
        if($user === null) return $next($request);
        if($user->id === $apiToken->user_id && ($apiToken->expired_at === null || $apiToken->expired_at >= Carbon::now())
            && $apiToken->ip === $request->server('HTTP_X_FORWARDED_FOR', $request->server('REMOTE_ADDR', null))) {
            $apiToken->expired_at = $apiToken->expired_at === null ? null : Carbon::now()->addMinutes(30);
            $apiToken->save();
            $request['token'] = $token;
            $request['now_user'] = $user;
            return $next($request);
        }
        return $next($request);
    }
}
