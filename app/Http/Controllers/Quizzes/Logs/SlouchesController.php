<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Slouch;
use Illuminate\Http\Request;

class SlouchesController extends Controller
{

    public function list(Request $request, $id)
    {
        $pulses = Slouch::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);

        return $pulses;
    }
}
