<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    public function index() {

        return view('admin.index.index.main');
    }

    public function indexJs() {
        $content = View::make('admin.index.index.script');

        return Response::make($content, 200)->header('Content-Type', 'application/javascript');
    }
}
