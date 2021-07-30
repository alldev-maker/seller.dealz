<?php

namespace App\Http\Controllers\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Model;
use App\Models\Quizzes\Quiz;
use App\Models\Settings\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass as StdClass;

class IndexController extends Controller
{

    /**
     * QuizzesController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Quiz',
            'plural'   => 'Quizzes',
        ];
    }

    public function index(Request $request)
    {
        if (auth()->user()->role->slug == 'test-taker') {
            return view('quizzes.index.testtaker');
        } else {
            return view('quizzes.index.index');
        }
    }

}
