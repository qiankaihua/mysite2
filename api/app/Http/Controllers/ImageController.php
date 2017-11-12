<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

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
        $file = file_get_contents(base_path('public/public/').$path);
        return (new Response(base64_encode($file), 200))->header('Content-type', 'image/*');
    }
}
