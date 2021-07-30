<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Emotion;
use Illuminate\Http\Request;

class EmotionsController extends Controller
{

    public function list(Request $request, $id)
    {
        if ($request->route('code') != null) {
            $code     = $request->route('code');
            $emotions = Emotion::ofResult($id)->ofEmotion($code)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        } else {
            $emotions = Emotion::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        }

        return $emotions;
    }
}
