<?php

namespace App\Http\Controllers\Quizzes\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Quizzes\Quiz\Section;
use App\Models\Settings\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass as StdClass;

class SectionsController extends Controller
{

    /**
     * SectionsController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Section',
            'plural'   => 'Sections',
        ];
    }

    /**
     * @param  Request  $request
     * @param  string   $id  Section ID
     *
     * @return JsonResponse
     */
    public function list(Request $request, string $id = '')
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $f['qid'] = !empty($id) ? $id : '';

        $query = with(new Section());

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
                if ($key == 'qid') {
                    $filterSQL .= ' quiz_id = ? ';
                } else {
                    $filterSQL              .= ' '.$key.' = ? ';
                    $appends['f['.$key.']'] = $value;
                }

                $where->params[] = $value;
            }
        }

        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1').' AND '.(!empty($filterSQL) ? $filterSQL : '1');


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('ordering', 'ASC')->orderBy('name', 'ASC');
        } else {
            foreach ($s as $key => $value) {
                $query                  = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s['.$key.']'] = $value;
            }
        }


        // ------------------------------------------------------------------
        // Pagination
        // ------------------------------------------------------------------

        if ($l > 0) {
            $sections = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $sections = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($sections) : $sections;
    }

    /**
     * Get the Section.
     *
     * @param $id
     * @param $sid
     *
     * @return Section
     */
    public function show($id, $sid)
    {
        unset($id);

        return Section::findOrFail($sid);
    }

    public function create(Request $request, $id)
    {
        $quizId = $id;
        $fields = $request->all();

        $message = new StdClass();
        $section = new Section();

        $query = with(new Section());

        DB::beginTransaction();

        try {
            $section->id          = $section->generateId();
            $section->name        = $fields['name'] ?? '';
            $section->description = $fields['description'] ?? '';
            $section->quiz_id     = $quizId;
            $section->type_id     = $fields['type']['id'] ?? '';
            $section->time_limit  = $fields['time_limit'] ?? 0;
            $section->ordering    = $query->whereRaw('quiz_id = ?', $quizId)->count() + 1;

            $section->save();

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'].' has been created.';

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to create a '.$this->name['singular'].'.';
            $message->reason  = $e->getMessage();
        }

        $request->session()->put('message', $message);

        if ($message->status === 'success') {
            return response()->json($section, 201);
        } else {
            return response()->json($message, 500);
        }
    }

    public function update(Request $request, $id, $sid)
    {
        $quizId = $id;
        $fields = $request->all();

        $message = new StdClass();
        $section = new Section();


        DB::beginTransaction();

        try {
            $section = Section::findOrFail($sid);

            $section->name        = $fields['name'] ?? '';
            $section->description = $fields['description'] ?? '';
            $section->quiz_id     = $quizId;
            $section->type_id     = $fields['type']['id'] ?? '';
            $section->time_limit  = $fields['time_limit'] ?? 0;

            $section->save();

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'].' has been updated.';

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to create a '.$this->name['singular'].'.';
            $message->reason  = $e->getMessage();
        }

        $request->session()->put('message', $message);

        if ($message->status === 'success') {
            return response()->json($section, 200);
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

    public function delete(Request $request, $id, $sid)
    {
        unset($request);
        unset($id);

        $message = new StdClass();

        /** @var Section $entity */
        $section = Section::findOrFail($sid);

        DB::beginTransaction();

        try {
            Section::destroy($section->id);

            $message->status = 'success';

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to delete a '.$this->name['singular'].'.';
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

        Section::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

    /**
     * Sort the sections.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    protected function sort(Request $request)
    {
        $message = new StdClass();
        $fields  = $request->all();

        if (!isset($fields['sections'])) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Sections.';
            $message->reason  = 'Field name [sections] is required.';
            return response()->json($message, 400);
        }

        $sections = $fields['sections'];

        if (!is_array($sections)) {
            $message->status  = 'danger';
            $message->content = 'Failed to sort Sections.';
            $message->reason  = 'Field name [sections] must be an array of Sections.';
            return response()->json($message, 400);
        }

        $i = 0;
        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionObj = Section::find($section['id']);
            $sectionObj->update(['ordering' => ++$i]);
        }

        return response()->json(['result' => 1,]);
    }

}
