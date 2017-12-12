<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Mockery\Exception;
use Ramsey\Uuid\Uuid;

class ImageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request, $folder_name, $img_name) {
        $path = $img_name;
        $path = str_replace("_", '.',$path);
        if($folder_name !== null) $path = $folder_name.'/'.$path;
        if (file_exists(base_path('public/public/') . $path)) $file = file_get_contents(base_path('public/public/') . $path);
        else abort(404);
        $mime_type = mime_content_type(base_path('public/public/') . $path);
//        header("content-type:image/*");
        header('content-type:' . $mime_type);
        header('Content-Disposition: inline; filename="' . $path . '"');
        echo $file;
        return;
//        return (new Response(base64_encode($file), 200))->header('Content-type', 'image/*');
    }
    public function Store(Request $request) {
        $this->validate($request, [
            'image' => 'required|image',
        ]);
        $extension = $request->file('image')->extension();
        $image_name = $request->file('image')->move('public/image', Uuid::uuid4()->toString().'.'.$extension);
        $image_name = str_replace("public/", "", $image_name);
        $image_name = str_replace(".", '_', $image_name);
        return response($image_name);
    }
}
