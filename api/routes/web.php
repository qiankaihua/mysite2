<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/check', ['middleware' => 'Check', function () use ($router) {
    return 'true';
}]);
$router->get('/addtime', ['middleware' => 'AddTime', function (\Illuminate\Support\Facades\Request $request) use ($router) {
    $now_user = $request->input('now_user', null);
    if($now_user === null) return [];
    $avatar = null;
    if(isset($user->avatar)) {
        $avatar = str_replace("public/", "", $now_user->avatar);
        $avatar = str_replace(".", '_', $avatar);
    }
    return response([
        'nickname' => $now_user->nickname,
        'gender' => $now_user->gender,
        'avatar' => $avatar,
    ]);
}]);


$router->group([
    'prefix' => 'auth',
], function () use ($router) {
    $router->post('login', 'UserController@Login');
    $router->post('register', 'UserController@Register');
});

$router->group([
    'prefix' => 'show',
    'middleware' => 'AddTime',
], function () use ($router) {
    $router->get('user/list', 'UserController@Show');
    $router->get('user/{user_id}', 'UserController@ShowDetail');

    $router->get('blog/list', 'BlogController@Show');
    $router->get('blog/{blog_id}', 'BlogController@ShowDetail');

    $router->get('category/list', 'CategoryController@Show');

    $router->get('tag/list', 'TagController@Show');

    $router->get('album/list', 'AlbumController@Show');
    $router->get('album/{album_id}', 'AlbumController@ShowDetail');

    $router->get('photo/list', 'PhotoController@Show');

    $router->get('comment/{blog_id}', 'CommentController@Show');
    $router->get('comment/reply/{comment_id}', 'CommentController@ShowRelpy');

    $router->get('img/{folder_name}/{img_name}', 'ImageController@Show');
});

$router->group([
    'prefix' => 'user',
    'middleware' => 'Check',
], function () use ($router) {
    $router->delete('{user_id}', 'UserController@Delete');
    $router->put('{user_id}', 'UserController@Restore');
    $router->put('{user_id}/security', 'UserController@ChangePassword');
    $router->put('{user_id}/info', 'UserController@Change');
});

$router->group([
    'prefix' => 'blog',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'BlogController@Store');
    $router->delete('{blog_id}', 'BlogController@Delete');
    $router->put('{blog_id}', 'BlogController@Restore');
    $router->put('{blog_id}/change', 'BlogController@Change');
    $router->post('{blog_id}/star', 'BlogController@Star');
    $router->delete('{blog_id}/star', 'BlogController@Star');
});

$router->group([
    'prefix' => 'tag',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'TagController@Store');
    $router->delete('{tag_id}', 'TagController@Delete');
});

$router->group([
    'prefix' => 'category',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'CategoryController@Store');
    $router->put('{category_id}', 'CategoryController@Change');
    $router->delete('{category_id}', 'CategoryController@Delete');
});


$router->group([
    'prefix' => 'album',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'AlbumController@Store');
    $router->put('{album_id}', 'AlbumController@Change');
    $router->delete('{album_id}', 'AlbumController@Delete');
});

$router->group([
    'prefix' => 'comment',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'CommentController@Store');
    $router->delete('{comment_id}', 'CommentController@Delete');
    $router->post('{comment_id}/star', 'CommentController@Star');
    $router->delete('{comment_id}/star', 'CommentController@Star');
});

$router->group([
    'prefix' => 'photo',
    'middleware' => 'Check',
], function () use ($router) {
    $router->post('/add', 'PhotoController@Store');
    $router->put('{photo_id}', 'PhotoController@Change');
    $router->delete('{photo_id}', 'PhotoController@Delete');
});