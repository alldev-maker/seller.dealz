<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Blink;
use Illuminate\Http\Request;

class BlinksController extends Controller
{

    public function list(Request $request, $id)
    {
        return Blink::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
    }
}
