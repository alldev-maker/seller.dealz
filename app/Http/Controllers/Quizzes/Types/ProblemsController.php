<?php

namespace App\Http\Controllers\Quizzes\Types;

use App\Models\Model;
use App\Models\Quizzes\Quiz;
use Cocur\Slugify\Slugify;
use stdClass as StdClass;
use App\Http\Controllers\Controller;
use App\Models\Quizzes\Types\Problem;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProblemsController extends Controller
{

    public function __construct()
    {
        $this->name = [
            'singular' => 'Problem Type',
            'plural'   => 'Problem Types',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Problem::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $type = $id == '' ? new Problem() : Problem::findOrFail($id);

        if ($id == '') {
            $type->key         = '';
            $type->name        = '';
            $type->description = '';
            $type->visible     = 1;
        }

        return view(sprintf('%s.form', Problem::PATH), ['problem' => $type]);
    }

    public function list(Request $request)
    {
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $appends = [];
        if ($l != $defaultPerPage->value) {
            $appends['l'] = $l;
        }

        $where         = new StdClass();
        $where->string = '';
        $where->params = [];

        $textSQL = '';
        if ($q != '') {
            $textSQL .= ' name LIKE ? ';

            $where->params[] = "%{$q}%";
        }

        $filterSQL = '';

        $where->string = (!empty($textSQL) ? $textSQL : '1').' AND '.(!empty($filterSQL) ? $filterSQL : '1');

        $query = with(new Problem);

        if (empty($s)) {
            $query = $query->orderBy('created_at', 'DESC');
        } else {
            foreach ($s as $key => $value) {
                $query = $query->orderBy($key, Model::SORT_ORDER[$value]);
            }
        }

        $appends = [];
        if ($q != '') {
            $appends['q'] = $q;
        }
        if (count($s) > 0) {
            foreach ($s as $key => $value) {
                $query                  = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s['.$key.']'] = $value;
            }
        }

        if ($l > 0) {
            $types = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $types = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($f);
        unset($p);

        return $l > 0 ? $this->render($types) : $types;
    }

    public function show($id)
    {
        return Problem::findOrFail($id);
    }

    public function create(Request $request)
    {
        $validation = Validator::make(
            $request->all(), ['name' => 'required']
        );

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors->toJson(), 400);
        } else {
            $slugify = new Slugify();
            $entity  = new Problem();

            $entity->name        = trim($request->get('name'));
            $entity->key         = $request->get('key') ?? $slugify->slugify($entity->name);
            $entity->description = $request->get('description');
            $entity->visible     = $request->get('visible');

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'].' has been created.';

            $request->session()->put('message', $message);

            return response()->json($entity, 201);
        }
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make(
            $request->all(), ['name' => 'required']
        );

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors->toJson(), 400);
        } else {
            $slugify = new Slugify();

            /** @var Problem $entity */
            $entity = Problem::findOrFail($id);

            $entity->name        = trim($request->get('name'));
            $entity->key         = $request->get('key') ?? $slugify->slugify($entity->name);
            $entity->description = $request->get('description');
            $entity->visible     = $request->get('visible');

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'].' has been updated.';

            $request->session()->put('message', $message);

            return response()->json($entity);
        }
    }

    public function updateMultiple(Request $request)
    {

    }

    public function delete(Request $request, $id)
    {
        unset($request);

        $entity = Problem::findOrFail($id);

        Problem::destroy($entity->key);
        Quiz\ProblemType::where('key', '=', $entity->key)->delete();

        return response()->json(['result' => 1]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Problem::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }
}
