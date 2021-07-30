<?php

namespace App\Http\Controllers\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Model;
use App\Models\Quizzes\Quiz;
use App\Models\Roster\Testtaker;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use stdClass as StdClass;

class InvitationsController extends Controller
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

    public function view($id)
    {
        $quiz = Quiz::findOrFail($id);

        $sections = Quiz\Section::ofQuiz($quiz->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();

        foreach ($sections as $section) {
            $sectionObj           = new StdClass();
            $sectionObj->id       = $section->id;
            $sectionObj->passages = [];

            $passages = Quiz\Passage::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();

            if ($quiz->shuffle_passages) {
                $passages = $passages->shuffle();
            }

            foreach ($passages as $passage) {
                $passageObj            = new StdClass();
                $passageObj->id        = $passage->id;
                $passageObj->questions = [];

                $questions = Quiz\Question::ofPassage($passage->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('question', 'ASC')->get();

                if ($quiz->shuffle_questions) {
                    $questions = $questions->shuffle();
                }

                foreach ($questions as $question) {
                    $questionObj          = new StdClass();
                    $questionObj->id      = $question->id;
                    $questionObj->choices = [];

                    $choices = $question->choices;

                    switch ($question->shuffle_choices) {
                        case 1: // No
                            break;
                        case 2: // Yes
                            shuffle($choices);
                            break;
                        case 0: // Inherit from Quiz Settings
                        default:
                            if ($quiz->shuffle_choices) {
                                shuffle($choices);
                            }
                    }

                    $letter = 'A';
                    foreach ($choices as $choice) {
                        $choice->letter = $letter;

                        $choiceObj         = new StdClass();
                        $choiceObj->id     = $choice->id;
                        $choiceObj->letter = $letter;

                        $questionObj->choices[] = $choiceObj;

                        $letter++;
                    }

                    $question->choices_computed = $choices;

                    $passageObj->questions[] = $questionObj;
                }

                $passage->questions     = $questions;
                $sectionObj->passages[] = $passageObj;
            }

            $section->passages = $passages;
        }

        $quiz->sections = $sections;

        return view(sprintf('%s.view', Quiz::PATH), ['quiz' => $quiz]);
    }

    public function list(Request $request)
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        /** @var User $user */
        $user      = auth()->user();
        $testtaker = Testtaker::where('user_id', '=', $user->id)->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $query = with(new Quiz());
        $query = $query->select('*')->join('roster_testtakers_quizzes', 'quizzes_quizzes.id', '=', 'roster_testtakers_quizzes.quiz_id');

        $appends = [];

        if ($l != $defaultPerPage->value) {
            $appends['l'] = $l;
        }


        // ------------------------------------------------------------------
        // Where Clause
        // ------------------------------------------------------------------

        $where         = new StdClass();
        $where->string = '';
        $where->params = [];

        // Query field
        // -----------------------------

        $textSQL = '';

        if ($q != '') {
            $textSQL .= ' quizzes_quizzes.name LIKE ? ';

            $where->params[] = "%{$q}%";

            $appends['q'] = $q;
        }

        // Filters
        // -----------------------------

        $filterSQL = '';
        if (count($f) > 0) {
            foreach ($f as $key => $value) {
                $filterSQL                  .= ' ' . $key . ' = ? ';
                $appends['f[' . $key . ']'] = $value;

                $where->params[] = $value;
            }
        }

        $filterSQL .= ' roster_testtakers_quizzes.testtaker_id = ?';

        $where->params[] = $testtaker->id;

        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (!empty($filterSQL) ? $filterSQL : '1');


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('quizzes_quizzes.created_at', 'DESC')->orderBy('quizzes_quizzes.name', 'ASC');
        } else {
            foreach ($s as $key => $value) {
                $query                      = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s[' . $key . ']'] = $value;
            }
        }


        // ------------------------------------------------------------------
        // Pagination
        // ------------------------------------------------------------------

        if ($l > 0) {
            $quizzes = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $quizzes = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($quizzes) : $quizzes;
    }

    public function show($id)
    {
        return Quiz::findOrFail($id);
    }
}
