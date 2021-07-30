<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $settings = '';
        $message  = $request->session()->get('message');
        $request->session()->forget('message');

        return view('admin.settings.index', ['message' => $message, 'settings' => $settings]);
    }

    public function update(Request $request)
    {

    }
}
