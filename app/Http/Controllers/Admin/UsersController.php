<?php

namespace App\Http\Controllers\Admin;

use App\Models\Model;
use Illuminate\Support\Facades\Hash;
use stdClass as StdClass;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->name = [
            'singular' => 'User',
            'plural'   => 'Users',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', User::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $user = $id == '' ? new User() : User::findOrFail($id);

        if ($id == '') {
            $user->id       = '';
            $user->name     = '';
            $user->email    = '';
            $user->password = '';
        }

        return view(sprintf('%s.form', User::PATH), ['user' => $user]);
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

        $query = with(new User);

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
                $query                      = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s[' . $key . ']'] = $value;
            }
        }

        if ($l > 0) {
            $users = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $users = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($f);
        unset($p);

        return $l > 0 ? $this->render($users) : $users;
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function exists(Request $request)
    {
        $acceptableFields = ['name', 'email'];

        $field = $request->get('field', '');
        $value = $request->get('value', '');
        $id    = $request->get('id', '');

        $message = new StdClass();

        if (empty($field)) {
            $message->result = null;
            $message->error  = 'Field name is missing.';
            return response()->json($message, 400);
        }

        if (!in_array($field, $acceptableFields)) {
            $message->result = null;
            $message->error  = 'Valid field name are [name] and [email].';
            return response()->json($message, 400);
        }

        if (empty($value)) {
            $message->result = null;
            $message->error  = 'Value is missing.';
            return response()->json($message, 400);
        }

        $clauses = [
            [$field, '=', $value],
        ];

        if ($id != '') {
            $clauses[] = ['id', '!=', $id];
        }

        $user = User::where($clauses)->first();

        $message->result = !empty($user);

        if ($message->result) {
            switch ($field) {
                case 'name':
                    $message->message = 'User name is already taken.';
                    break;
                case 'email':
                    $message->message = 'Email address already exists.';
                    break;
            }
        }

        return response()->json($message, 200);
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
            $entity = new User();

            $entity->id           = $entity->generateId();
            $entity->name         = $request->get('name');
            $entity->password     = trim($request->get('password')) != '' ? Hash::make(trim($request->get('password'))) : Hash::make('password');
            $entity->email        = $request->get('email');
            $entity->role_id      = $request->get('role')['id'];
            $entity->nice_name    = $request->get('nice_name');
            $entity->notes        = $request->get('notes');

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
            /** @var User $entity */
            $entity = User::findOrFail($id);

            $entity->name         = $request->get('name');
            $entity->password     = trim($request->get('password')) != '' ? Hash::make(trim($request->get('password'))) : $entity->password;
            $entity->email        = $request->get('email');
            $entity->role_id      = $request->get('role')['id'];
            $entity->nice_name    = $request->get('nice_name');
            $entity->notes        = $request->get('notes');

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

        /** @var User $entity */
        $entity = User::findOrFail($id);

        User::destroy($entity->id);

        return response()->json(['result' => 1]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        User::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }
}
