<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Show(Request $request, $img_name) {
        $path = $img_name;
        $path = str_replace("_", '.',$path);
        return response()->file(base_path('storage/app/public/').$path);
    }
}
