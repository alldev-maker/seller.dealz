<?php

namespace App\Http\Controllers\Quizzes\Types;

use App\Models\Model;
use Cocur\Slugify\Slugify;
use stdClass as StdClass;
use App\Http\Controllers\Controller;
use App\Models\Quizzes\Types\Section;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionsController extends Controller
{

    public function __construct()
    {
        $this->name = [
            'singular' => 'Section Type',
            'plural'   => 'Section Types',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Section::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $type = $id == '' ? new Section() : Section::findOrFail($id);

        if ($id == '') {
            $type->id   = '';
            $type->name = '';
        }

        return view(sprintf('%s.form', Section::PATH), ['section' => $type]);
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

        $query = with(new Section);

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
        return Section::findOrFail($id);
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
            $entity  = new Section();

            $entity->id          = $entity->generateId();
            $entity->name        = $request->get('name');
            $entity->slug        = $request->get('slug') ?? $slugify->slugify($entity->name);
            $entity->description = $request->get('description');

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

            /** @var Section $entity */
            $entity = Section::findOrFail($id);

            $entity->name        = $request->get('name');
            $entity->slug        = $request->get('slug') ?? $slugify->slugify($entity->name);
            $entity->description = $request->get('description');

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

        /** @var Section $entity */
        $entity = Section::findOrFail($id);

        Section::destroy($entity->id);

        return response()->json(['result' => 1]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Section::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }
}
