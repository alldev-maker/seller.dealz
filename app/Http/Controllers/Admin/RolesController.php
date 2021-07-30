<?php

namespace App\Http\Controllers\Admin;

use App\Models\Model;
use stdClass as StdClass;
use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\Settings\Setting;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{

    public function __construct()
    {
        $this->name = [
            'singular' => 'Role',
            'plural'   => 'Roles',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Role::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $role = $id == '' ? new Role() : Role::findOrFail($id);

        if ($id == '') {
            $role->id   = '';
            $role->slug = '';
            $role->name = '';
        }

        return view(sprintf('%s.form', Role::PATH), ['role' => $role]);
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
            $textSQL .= ' slug LIKE ? OR name LIKE ? ';

            $where->params[] = "%{$q}%";
            $where->params[] = "%{$q}%";
        }

        $filterSQL = '';

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (!empty($filterSQL) ? $filterSQL : '1');

        $query = with(new Role);

        if (empty($s)) {
            $query = $query->orderBy('created_at', 'DESC')->orderBy('sequence', 'DESC');
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
                $query                      = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s[' . $key . ']'] = $value;
            }
        }

        if ($l > 0) {
            $roles = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $roles = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($f);
        unset($p);

        return $l > 0 ? $this->render($roles) : $roles;
    }

    public function show($id)
    {
        return Role::findOrFail($id);
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

            $name = $request->get('name');
            $slug = $request->get('slug');
            $slug = empty($slug) ? $slugify->slugify($name) : $slug;

            $entity = new Role();

            $entity->id          = $entity->generateId();
            $entity->slug        = $slug;
            $entity->name        = $name;
            $entity->description = $request->get('description');
            $entity->prefix      = $request->get('prefix');
            //$entity->sequence = $entity->cou

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been created.';

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

            $name = $request->get('name');
            $slug = $request->get('slug');
            $slug = empty($slug) ? $slugify->slugify($name) : $slug;

            /** @var Role $entity */
            $entity = Role::findOrFail($id);

            $entity->slug        = $slug;
            $entity->name        = $name;
            $entity->description = $request->get('description');
            $entity->prefix      = $request->get('prefix');

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been updated.';

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

        /** @var Role $entity */
        $entity = Role::findOrFail($id);

        Role::destroy($entity->id);

        return response()->json(['result' => 1]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Role::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }
}
