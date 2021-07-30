<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Pulse;
use Illuminate\Http\Request;

class PulsesController extends Controller
{

    public function list(Request $request, $id)
    {
        $pulses = Pulse::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);

        return $pulses;
    }
}
