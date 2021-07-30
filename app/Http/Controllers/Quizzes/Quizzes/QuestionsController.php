<?php

namespace App\Http\Controllers\Quizzes\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Quizzes\Quiz\Choice;
use App\Models\Quizzes\Quiz\ProblemType;
use App\Models\Quizzes\Quiz\Question;
use App\Models\Quizzes\Types\Problem;
use App\Models\Settings\Setting;
use Cocur\Slugify\Slugify;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass as StdClass;

class QuestionsController extends Controller
{

    /**
     * QuestionsController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Question',
            'plural'   => 'Questions',
        ];
    }

    /**
     * @param  Request  $request
     * @param  string   $id   Quiz ID
     * @param  string   $sid  Section ID
     * @param  string   $pid  Passage ID
     *
     * @return JsonResponse
     */
    public function list(Request $request, string $id = '', $sid = '', $pid = '')
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $f['qid'] = !empty($id) ? $id : '';
        $f['sid'] = !empty($sid) ? $sid : '';
        $f['pid'] = !empty($pid) ? $pid : '';

        $query = with(new Question());

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

        $filterSQL = [];
        if (count($f) > 0) {
            foreach ($f as $key => $value) {
                if ($key == 'qid') {
                    $filterSQL[] = ' quiz_id = ? ';
                } else {
                    if ($key == 'sid') {
                        $filterSQL[] = ' section_id = ? ';
                    } else {
                        if ($key == 'pid') {
                            $filterSQL[] = ' passage_id = ? ';
                        } else {
                            $filterSQL[]                = ' ' . $key . ' = ? ';
                            $appends['f[' . $key . ']'] = $value;
                        }
                    }
                }

                $where->params[] = $value;


            }
        }

        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (count($filterSQL) > 0 ? implode('AND', $filterSQL) : '1');


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('ordering', 'ASC');
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
            $questions = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $questions = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($questions) : $questions;
    }

    /**
     * Get the Question.
     *
     * @param $id
     * @param $sid
     * @param $pid
     * @param $qid
     *
     * @return Question
     */
    public function show($id, $sid, $pid, $qid)
    {
        unset($id, $sid, $pid);

        return Question::findOrFail($qid);
    }

    public function create(Request $request, $id, $sid, $pid)
    {
        $quizId    = $id;
        $sectionId = $sid;
        $passageId = $pid;
        $fields    = $request->all();

        $message  = new StdClass();
        $question = new Question();

        $query = with(new Question());

        DB::beginTransaction();

        try {
            // Question
            // -----------------------------

            $question->id              = $question->generateId();
            $question->question        = $fields['question'] ?? '';
            $question->type            = $fields['type']['id'] ?? 1;
            $question->shuffle_choices = $fields['shuffle_choices'] ?? 0;
            $question->difficulty      = $fields['difficulty'] ?? 1;
            $question->explain_video   = $fields['explain_video'] ?? '';
            $question->points          = $fields['points'] ?? 0;
            $question->quiz_id         = $quizId;
            $question->section_id      = $sectionId;
            $question->passage_id      = $passageId;
            $question->ordering        = $query->whereRaw('passage_id = ?', $passageId)->count() + 1;

            $question->save();

            // Problem Types
            // -----------------------------

            foreach ($fields['problem_types'] as $problemtype) {
                $problem = $this->getProblemType($problemtype);

                if (!$problem) {
                    continue;
                }

                $ptObj = new ProblemType();

                $ptObj->id          = $ptObj->generateId();
                $ptObj->quiz_id     = $quizId;
                $ptObj->section_id  = $sectionId;
                $ptObj->passage_id  = $passageId;
                $ptObj->question_id = $question->id;
                $ptObj->key         = $problem->key;

                $ptObj->save();
            }

            // Choices
            // -----------------------------

            $totalPoints = 0;
            $ordering    = 0;
            foreach ($fields['choices'] as $choice) {
                $choiceObj = new Choice();

                $choiceObj->id          = $choiceObj->generateId();
                $choiceObj->question_id = $question->id;
                $choiceObj->choice      = $choice['choice'];
                $choiceObj->is_correct  = (int) $choice['is_correct'];
                $choiceObj->points      = (int) $choice['points'];
                $choiceObj->ordering    = ++$ordering;
                $totalPoints            += $choiceObj->points;

                $choiceObj->save();
            }

            $question->points = $totalPoints;
            $question->save();

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
            return response()->json($question, 201);
        } else {
            return response()->json($message, 500);
        }
    }

    public function update(Request $request, $id, $sid, $pid, $qid)
    {
        $quizId     = $id;
        $sectionId  = $sid;
        $passageId  = $pid;
        $questionId = $qid;
        $fields     = $request->all();

        $message  = new StdClass();
        $question = new Question();


        DB::beginTransaction();

        try {
            $question = Question::findOrFail($questionId);

            // Question
            // -----------------------------

            $question->question        = $fields['question'] ?? '';
            $question->type            = $fields['type']['id'] ?? 1;
            $question->shuffle_choices = $fields['shuffle_choices'] ?? 0;
            $question->difficulty      = $fields['difficulty'] ?? 1;
            $question->explain_video   = $fields['explain_video'] ?? '';
            $question->points          = $fields['points'] ?? 0;
            $question->quiz_id         = $quizId;
            $question->section_id      = $sectionId;
            $question->passage_id      = $passageId;

            $question->save();

            // Problem Types
            // -----------------------------

            ProblemType::ofQuestion($question->id)->forceDelete();

            foreach ($fields['problem_types'] as $problemtype) {
                $problem = $this->getProblemType($problemtype);

                if (!$problem) {
                    continue;
                }

                $ptObj = new ProblemType();

                $ptObj->id          = $ptObj->generateId();
                $ptObj->quiz_id     = $quizId;
                $ptObj->section_id  = $sectionId;
                $ptObj->passage_id  = $passageId;
                $ptObj->question_id = $question->id;
                $ptObj->key         = $problem->key;

                $ptObj->save();
            }

            // Choices
            // -----------------------------

            Choice::ofQuestion($question->id)->forceDelete();

            $totalPoints = 0;
            $ordering    = 0;
            foreach ($fields['choices'] as $choice) {
                $choiceObj = new Choice();

                $choiceObj->id          = empty($choice['id']) ? $choiceObj->generateId() : $choice['id'];
                $choiceObj->question_id = $question->id;
                $choiceObj->choice      = $choice['choice'];
                $choiceObj->is_correct  = (int) $choice['is_correct'];
                $choiceObj->points      = (int) $choice['points'];
                $choiceObj->ordering    = ++$ordering;
                $totalPoints            += $choiceObj->points;

                $choiceObj->save();
            }

            $question->points = $totalPoints;
            $question->save();

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been updated.';

        } catch (Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }

        $request->session()->put('message', $message);

        if ($message->status === 'success') {
            return response()->json($question, 200);
        } else {
            return response()->json($message, 500);
        }

    }

    public function updateMultiple(Request $request)
    {
        $action = $request->get('action');

        switch ($action) {
            case 'sort':
                return $this->sort($request);
            default:
                return response()->json(null);
        }
    }

    public function delete(Request $request, $id, $sid, $pid, $qid)
    {
        unset($request);
        unset($id);
        unset($sid);
        unset($pid);

        $message = new StdClass();

        /** @var Question $entity */
        $question = Question::findOrFail($qid);

        DB::beginTransaction();

        try {
            $question->delete();
            Choice::ofQuestion($qid)->forceDelete();
            ProblemType::ofQuestion($qid)->forceDelete();

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
        $ids = $request->get('ids');

        Question::whereIn('id', $ids)->delete();
        Choice::whereIn('question_id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

    /**
     * Sort the questions.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    protected function sort(Request $request)
    {
        $message = new StdClass();
        $fields  = $request->all();

        if (!isset($fields['questions'])) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Questions.';
            $message->reason  = 'Field name [questions] is required.';
            return response()->json($message, 400);
        }

        $questions = $fields['questions'];

        if (!is_array($questions)) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Questions.';
            $message->reason  = 'Field name [questions] must be an array of Questions.';
            return response()->json($message, 400);
        }

        $i = 0;
        foreach ($questions as $question) {
            if (!is_array($question)) {
                continue;
            }

            $questionObj = Question::find($question['id']);
            $questionObj->update(['ordering' => ++$i]);
        }

        return response()->json(['result' => 1,]);
    }

    /**
     * Returns the Problem Type. If the Problem Type is new, add to the table.
     * If it exists, retrieve from rows.
     *
     * @param  array  $problemtype
     *
     * @return Problem|null
     */
    protected function getProblemType($problemtype)
    {
        if (!isset($problemtype['key']) && !isset($problemtype['value'])) {
            return null;
        }

        if (empty($problemtype['key']) && !empty($problemtype['value'])) {
            $slugify = new Slugify();

            $problemtype['key'] = $slugify->slugify(trim($problemtype['value']));
        }

        $problem = Problem::find($problemtype['key']);

        if (!$problem) {
            $problem = new Problem();

            $problem->key  = $problemtype['key'];
            $problem->name = $problemtype['value'];

            $problem->save();
        }

        return $problem;
    }

}
