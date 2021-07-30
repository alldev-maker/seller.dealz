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

class QuizzesController extends Controller
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
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Quiz::PATH), ['message' => $message]);
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

    public function form(Request $request, $id = '')
    {
        $quiz = $id == '' ? new Quiz() : Quiz::findOrFail($id);

        $message = $request->session()->get('message');
        $request->session()->forget('message');

        if ($id == '') {
            $quiz->id          = '';
            $quiz->name        = '';
            $quiz->description = '';
        }

        return view(sprintf('%s.form', Quiz::PATH), ['quiz' => $quiz, 'message' => $message]);
    }

    public function questionnaire(Request $request, $id = '')
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        $quiz           = Quiz::findOrFail($id);
        $quiz->sections = [];

        return view(sprintf('%s.questionnaire', Quiz::PATH), ['quiz' => $quiz, 'message' => $message]);
    }

    public function settings(Request $request, $id = '')
    {
        $quiz = Quiz::findOrFail($id);

        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.settings', Quiz::PATH), ['quiz' => $quiz, 'message' => $message]);
    }

    public function invitations(Request $request, $id = '')
    {
        $quiz = Quiz::findOrFail($id);

        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.invitations', Quiz::PATH), ['quiz' => $quiz, 'message' => $message]);
    }

    public function list(Request $request)
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        /** @var User $user */
        $user = auth()->user();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $query = with(new Quiz());

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
            $textSQL .= ' name LIKE ? ';

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

        // Role check
        if (!($user->hasRole('admin') || $user->hasRole('developer'))) {
            $filterSQL       .= 'user_id = ?';
            $where->params[] = $user->id;
        }

        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (!empty($filterSQL) ? $filterSQL : '1');


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('created_at', 'DESC')->orderBy('name', 'ASC');
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

    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $fields = $request->all();

        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'name.required' => 'Quiz name is required.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors, 400);
        } else {
            $message = new StdClass();
            $quiz    = new Quiz();

            DB::beginTransaction();

            try {
                $quiz = new Quiz();

                $quiz->id      = $quiz->generateId();
                $quiz->name    = $fields['name'] ?? '';
                $quiz->user_id = $user->id;

                $quiz->save();

                DB::commit();

                $message->status  = 'success';
                $message->content = $this->name['singular'] . ' has been created.';

            } catch (Exception $e) {
                DB::rollback();

                $message->status  = 'danger';
                $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
                $message->reason  = $e->getMessage();
            }

            $request->session()->put('message', $message);

            if ($message->status === 'success') {
                return response()->json($quiz, 201);
            } else {
                return response()->json($message, 500);
            }

        }
    }

    public function update(Request $request, $id)
    {
        $fields  = $request->all();
        $message = new StdClass();

        if (!isset($fields['action'])) {
            $message->status  = 'danger';
            $message->content = 'Failed to update ' . $this->name['singular'] . '.';
            $message->reason  = 'Field [action] is missing.';

            return response()->json($message, 400);
        }

        switch ($fields['action']) {
            case 'update-about':
            default:
                return $this->saveAbout($request, $id);
            case 'update-settings':
                return $this->saveSettings($request, $id);
            case 'copy':
                return $this->copy($id);
        }
    }

    public function updateMultiple()
    {
        $message = new StdClass();

        $message->status  = 'danger';
        $message->content = 'Method not allowed.';
        $message->reason  = 'Updating the ' . $this->name['singular'] . ' collection is not implemented.';

        return response()->json($message, 405);
    }

    public function delete(Request $request, $id)
    {
        unset($request);

        $message = new StdClass();

        DB::beginTransaction();

        try {
            $this->deleteSingle($id);

            $message->status = 'success';

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to delete a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }


        if ($message->status === 'success') {
            return response()->json(['result' => 1]);
        } else {
            return response()->json($message, 500);
        }

    }

    public function deleteMultiple(Request $request)
    {
        $ids     = $request->get('ids');
        $message = new StdClass();

        DB::beginTransaction();

        try {
            foreach ($ids as $id) {
                $this->deleteSingle($id);
            }

            $message->status = 'success';

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to delete a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }


        if ($message->status === 'success') {
            return response()->json(['result' => 1]);
        } else {
            return response()->json($message, 500);
        }
    }

    /**
     * Deployment check before enabling the Quiz online.
     *
     * @param  Request  $request
     * @param  string   $id
     *
     * @return JsonResponse
     */
    public function depcheck(Request $request, $id)
    {
        $quiz    = Quiz::find($id);
        $message = new StdClass();

        if (!$quiz) {
            $message->code    = '404';
            $message->status  = 'danger';
            $message->content = 'Quiz not found.';
            $message->reason  = 'Quiz not found.';

            return response()->json($message, 404);
        }

        // ------------------------------------------------------------------
        // Check if there are Questions
        // ------------------------------------------------------------------

        $q_count = Quiz\Question::ofQuiz($quiz->id)->count('id');

        if ($q_count < 1) {
            $message->code    = '422.1';
            $message->status  = 'danger';
            $message->content = 'There are no Questions in this Quiz.';
            $message->reason  = [
                [
                    'message' => 'There are no Questions in this Quiz.',
                ],
            ];


            return response()->json($message, 422);
        }

        // ------------------------------------------------------------------
        // Check if there are at least two Choices and at least one
        // correct Choice
        // ------------------------------------------------------------------

        $questions = Quiz\Question::ofQuiz($quiz->id)->get();

        $error_count   = 0;
        $error_choices = [];
        $error_correct = [];

        foreach ($questions as $question) {
            $ch_count = Quiz\Choice::ofQuestion($question->id)->count('id');
            if ($ch_count < 2) {
                $question->choice_count = $ch_count;
                $error_choices[]        = $question;
                $error_count++;
            } else {
                $cr_count = Quiz\Choice::ofQuestion($question->id)->ofCorrect()->count('id');
                if ($cr_count < 1) {
                    $error_correct[] = $question;
                    $error_count++;
                }
            }
        }

        if ($error_count > 0) {
            $message->code    = '422.2';
            $message->status  = 'danger';
            $message->content = 'Quiz validation failed.';
            $message->reason  = [];

            $ech = count($error_choices);
            if ($ech > 0) {
                $error            = new StdClass();
                $error->message   = ($ech > 1) ?
                    sprintf('There are %d questions that have less than two (2) choices.', $ech) :
                    'There is 1 question that has less than two (2) choices.';
                $error->questions = [];
                foreach ($error_choices as $question) {
                    $error->questions[] = $question;
                }

                $message->reason[] = $error;
            }

            $ecr = count($error_correct);
            if ($ecr > 0) {
                $error = new StdClass();

                $error->message   = ($ecr > 1) ?
                    sprintf('There are %d questions that have no correct answers.', $ecr) :
                    'There is 1 question that has no correct answer.';
                $error->questions = [];
                foreach ($error_correct as $question) {
                    $error->questions[] = $question;
                }

                $message->reason[] = $error;
            }

            return response()->json($message, 422);
        } else {
            $message->code    = '200';
            $message->status  = 'success';
            $message->content = 'OK';
            $message->reason  = 'Quiz has been validated.';

            return response()->json($message);
        }
    }

    protected function saveAbout(Request $request, $id)
    {
        /** @var User $user */
        $user   = auth()->user();
        $fields = $request->all();

        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'name.required' => 'Quiz name is required.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors, 400);
        } else {
            $message = new StdClass();
            $quiz    = Quiz::findOrFail($id);

            DB::beginTransaction();

            try {
                $quiz->name        = $fields['name'] ?? '';
                $quiz->description = $fields['description'] ?? '';

                $is_admin = $user->role->slug == 'admin' || $user->role->slug == 'developer';

                if ($is_admin && isset($fields['scoring_type'])) {
                    $quiz->scoring_type_id = $fields['scoring_type']['id'];
                }

                if ($is_admin && isset($fields['user'])) {
                    $quiz->user_id = $fields['user']['id'];
                }

                $quiz->content_upload = $fields['content_upload'] ?? '';
                $quiz->content_after  = $fields['content_after'] ?? '';

                $quiz->save();

                DB::commit();

                $message->status  = 'success';
                $message->content = $this->name['singular'] . ' has been updated.';

            } catch (Exception $e) {
                DB::rollback();

                $message->status  = 'danger';
                $message->content = 'Failed to update the ' . $this->name['singular'] . '.';
                $message->reason  = $e->getMessage();
            }

            $request->session()->put('message', $message);

            if ($message->status === 'success') {
                return response()->json($quiz, 201);
            } else {
                return response()->json($message, 500);
            }
        }
    }

    protected function saveSettings(Request $request, $id)
    {
        $fields = $request->all();

        $message = new StdClass();
        $quiz    = Quiz::findOrFail($id);

        DB::beginTransaction();

        try {
            $quiz->enabled           = $fields['enabled'] ?? false;
            $quiz->duration          = $fields['duration'] ?? 0;
            $quiz->auto_submit       = $fields['auto_submit'] ?? false;
            $quiz->shuffle_passages  = $fields['shuffle_passages'] ?? false;
            $quiz->shuffle_questions = $fields['shuffle_questions'] ?? false;
            $quiz->shuffle_choices   = $fields['shuffle_choices'] ?? false;
            $quiz->allow_guests      = $fields['allow_guests'] ?? false;
            $quiz->multiple_takes    = $fields['multiple_takes'] ?? false;

            $quiz->save();

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' settings has been updated.';

        } catch (Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to update the ' . $this->name['singular'] . ' settings.';
            $message->reason  = $e->getMessage();
        }

        $request->session()->put('message', $message);

        if ($message->status === 'success') {
            return response()->json($quiz, 200);
        } else {
            return response()->json($message, 500);
        }
    }

    protected function copy($id)
    {
        $message = new StdClass();
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $orig = Quiz::find($id);
            $copy = $orig->replicate();

            $copy->id              = $copy->generateId();
            $copy->name            = $orig->name . ' (Copy)';
            $copy->user_id         = $user->id;

            $copy->save();

            $orig_sections = Quiz\Section::ofQuiz($orig->id)->get();
            foreach ($orig_sections as $orig_sect) {
                $copy_sect = $orig_sect->replicate();

                $copy_sect->id      = $copy_sect->generateId();
                $copy_sect->quiz_id = $copy->id;
                $copy_sect->name    = $orig_sect->name;

                $copy_sect->save();

                $orig_passages = Quiz\Passage::ofSection($orig_sect->id)->get();
                foreach ($orig_passages as $orig_pass) {
                    $copy_pass = $orig_pass->replicate();

                    $copy_pass->id         = $copy_pass->generateId();
                    $copy_pass->quiz_id    = $copy->id;
                    $copy_pass->section_id = $copy_sect->id;

                    $copy_pass->save();

                    $orig_questions = Quiz\Question::ofPassage($orig_pass->id)->get();
                    foreach ($orig_questions as $orig_qstn) {
                        $copy_qstn = $orig_qstn->replicate();

                        $copy_qstn->id         = $copy_qstn->generateId();
                        $copy_qstn->quiz_id    = $copy->id;
                        $copy_qstn->section_id = $copy_sect->id;
                        $copy_qstn->passage_id = $copy_pass->id;

                        $copy_qstn->save();

                        $orig_choices = Quiz\Choice::ofQuestion($orig_qstn->id)->get();
                        foreach ($orig_choices as $orig_choice) {
                            $copy_choice = $orig_choice->replicate();

                            $copy_choice->id          = $copy_choice->generateId();
                            $copy_choice->question_id = $copy_qstn->id;

                            $copy_choice->save();
                        }

                        $orig_probtypes = Quiz\ProblemType::ofQuestion($orig_qstn->id)->get();
                        foreach ($orig_probtypes as $orig_probtype) {
                            $copy_probtype = $orig_probtype->replicate();

                            $copy_probtype->id          = $copy_probtype->generateId();
                            $copy_probtype->quiz_id     = $copy->id;
                            $copy_probtype->section_id  = $copy_sect->id;
                            $copy_probtype->passage_id  = $copy_pass->id;
                            $copy_probtype->question_id = $copy_qstn->id;

                            $copy_probtype->save();
                        }
                    }
                }
            }

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been copied.';
        } catch (Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to delete a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }


        if ($message->status === 'success') {
            return response()->json(['result' => 1]);
        } else {
            return response()->json($message, 500);
        }
    }

    protected function deleteSingle($id)
    {
        $quiz = Quiz::findOrFail($id);

        Quiz\Section::ofQuiz($id)->forceDelete();
        Quiz\Passage::ofQuiz($id)->forceDelete();

        $questions = Quiz\Question::ofQuiz($id)->get();
        foreach ($questions as $question) {
            Quiz\Choice::ofQuestion($question->id)->forceDelete();
            Quiz\ProblemType::ofQuestion($question->id)->forceDelete();

            $question->forceDelete();
        }

        Quiz\Question::ofQuiz($id)->forceDelete();

        $quiz->forceDelete();
    }
}
