<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Dilation;
use Illuminate\Http\Request;

class DilationsController extends Controller
{

    public function list(Request $request, $id)
    {
        $dilations = Dilation::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);

        return $dilations;
    }
}
