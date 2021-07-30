<?php

namespace App\Http\Controllers\Quizzes\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Quizzes\Quiz\Choice;
use App\Models\Quizzes\Quiz\Passage;
use App\Models\Quizzes\Quiz\ProblemType;
use App\Models\Quizzes\Quiz\Question;
use App\Models\Settings\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass as StdClass;

class PassagesController extends Controller
{

    /**
     * PassagesController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Passage',
            'plural'   => 'Passages',
        ];
    }

    /**
     * @param  Request  $request
     * @param  string   $id   Quiz ID
     * @param  string   $sid  Section ID
     *
     * @return JsonResponse
     */
    public function list(Request $request, string $id = '', $sid = '')
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

        $query = with(new Passage());

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
                        $filterSQL[] = ' ' . $key . ' = ? ';

                        $appends['f[' . $key . ']'] = $value;
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
            $query = $query->orderBy('ordering', 'ASC')->orderBy('name', 'ASC');
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
            $passages = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $passages = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($passages) : $passages;
    }

    /**
     * Get the Passage.
     *
     * @param $id
     * @param $sid
     *
     * @return Passage
     */
    public function show($id, $sid)
    {
        unset($id);

        return Passage::findOrFail($sid);
    }

    public function create(Request $request, $id, $sid)
    {
        $quizId    = $id;
        $sectionId = $sid;
        $fields    = $request->all();

        $message = new StdClass();
        $passage = new Passage();

        $query = with(new Passage());

        DB::beginTransaction();

        try {
            $passage->id          = $passage->generateId();
            $passage->name        = $fields['name'] ?? '';
            $passage->description = $fields['description'] ?? '';
            $passage->qa_layout   = $fields['qa_layout'] ?? 'nqa';
            $passage->content     = $fields['content'] ?? '';
            $passage->quiz_id     = $quizId;
            $passage->section_id  = $sectionId;
            $passage->ordering    = $query->whereRaw('section_id = ?', $sectionId)->count() + 1;

            $passage->save();

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
            return response()->json($passage, 201);
        } else {
            return response()->json($message, 500);
        }
    }

    public function update(Request $request, $id, $sid, $pid)
    {
        $quizId    = $id;
        $sectionId = $sid;
        $passageId = $pid;
        $fields    = $request->all();

        $message = new StdClass();
        $passage = new Passage();


        DB::beginTransaction();

        try {
            $passage = Passage::findOrFail($passageId);

            $passage->name        = $fields['name'] ?? '';
            $passage->description = $fields['description'] ?? '';
            $passage->qa_layout   = $fields['qa_layout'] ?? 'nqa';
            $passage->content     = $fields['content'] ?? '';
            $passage->quiz_id     = $quizId;
            $passage->section_id  = $sectionId;

            $passage->save();

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
            return response()->json($passage, 200);
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

    public function delete(Request $request, $id, $sid, $pid)
    {
        unset($request);
        unset($id);
        unset($sid);

        $message = new StdClass();

        /** @var Passage $entity */
        $passage = Passage::findOrFail($pid);

        DB::beginTransaction();

        try {
            $questions = Question::ofPassage($pid);
            foreach ($questions as $question) {
                Choice::ofQuestion($question->id)->forceDelete();
                ProblemType::ofQuestion($question->id)->forceDelete();

                $question->forceDelete();
            }

            $passage->forceDelete();

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

        Passage::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

    /**
     * Sort the passages.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    protected function sort(Request $request)
    {
        $message = new StdClass();
        $fields  = $request->all();

        if (!isset($fields['passages'])) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Passages.';
            $message->reason  = 'Field name [passages] is required.';
            return response()->json($message, 400);
        }

        $passages = $fields['passages'];

        if (!is_array($passages)) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Passages.';
            $message->reason  = 'Field name [passages] must be an array of Passages.';
            return response()->json($message, 400);
        }

        $i = 0;
        foreach ($passages as $passage) {
            if (!is_array($passage)) {
                continue;
            }

            $passageObj = Passage::find($passage['id']);
            $passageObj->update(['ordering' => ++$i]);
        }

        return response()->json(['result' => 1,]);
    }

}
