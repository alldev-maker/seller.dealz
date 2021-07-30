<?php

namespace App\Http\Controllers\Roster;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Roster\Testtaker;
use App\Models\Settings\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass as StdClass;

class TesttakersController extends Controller
{
    public function __construct()
    {
        $this->name = [
            'singular' => 'Test Taker',
            'plural'   => 'Test Takers',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Testtaker::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $testtaker = $id == '' ? new Testtaker() : Testtaker::findOrFail($id);

        if ($id == '') {
            $testtaker->id          = '';
            $testtaker->email       = '';
            $testtaker->family_name = '';
            $testtaker->given_name  = '';
            $testtaker->suffix      = '';

            $testtaker->user = new User();
        }

        return view(sprintf('%s.form', Testtaker::PATH), ['testtaker' => $testtaker]);
    }

    public function list(Request $request)
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);

        $appends = [];
        if ($l != $defaultPerPage->value) {
            $appends['l'] = $l;
        }

        if ($q != '') {
            $appends['q'] = $q;

            $qry        = 'user_id = ? OR family_name LIKE ? OR given_name LIKE ? OR email LIKE ?';
            $prm        = [$q, "%{$q}%", "%{$q}%", "%{$q}%"];
            $testtakers = Testtaker::whereRaw($qry, $prm)->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $testtakers = Testtaker::paginate($l, ['*'], 'p')->appends($appends);
        }

        unset($p);

        return $this->render($testtakers);
    }

    public function show($id)
    {
        return Testtaker::findOrFail($id);
    }

    public function create(Request $request)
    {
        $fields = $request->all();

        $rules    = [
            'family_name' => 'required',
            'given_name'  => 'required',
            'email'       => 'required',
        ];
        $messages = [
            'family_name.required' => 'Family name is required.',
            'given_name.required'  => 'Given name is required.',
            'email.required'       => 'Email address is required.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors, 400);
        } else {
            $message   = new StdClass();
            $testtaker = new Testtaker();
            DB::beginTransaction();

            try {
                $testtaker = new Testtaker();

                $testtaker->id             = $testtaker->generateId();
                $testtaker->given_name     = $fields['given_name'] ?? '';
                $testtaker->family_name    = $fields['family_name'] ?? '';
                $testtaker->suffix         = $fields['suffix'] ?? '';
                $testtaker->nickname       = $fields['nickname'] ?? '';
                $testtaker->nice_name      = $fields['nice_name'] ?? ($fields['nickname'] . ' ' . $fields['family_name']);
                $testtaker->email          = $fields['email'] ?? '';
                $testtaker->phone_mobile   = $fields['phone_mobile'] ?? '';
                $testtaker->phone_landline = $fields['phone_landline'] ?? '';

                $testtaker->school      = $fields['school'] ?? '';
                $testtaker->address     = $fields['address'] ?? '';
                $testtaker->locality    = $fields['locality'] ?? '';
                $testtaker->county      = $fields['county'] ?? '';
                $testtaker->state       = $fields['state'] ?? '';
                $testtaker->country_id  = $fields['country']['id'] ?? '';
                $testtaker->postal_code = $fields['postal_code'] ?? '';

                $testtaker->save();

                $user = new User();

                $user->id       = $user->generateId();
                $user->name     = $fields['user']['name'] ?? '';
                $user->password = trim($fields['user']['password']) != '' ? Hash::make(trim($fields['user']['password'])) : Hash::make('password');
                $user->email    = $fields['email'] ?? '';
                $user->role_id  = $fields['role']['id'] ?? Setting::find('testtaker.role')->value->id;

                $user->save();

                $testtaker->user_id = $user->id;

                $testtaker->update();

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
                return response()->json($testtaker, 201);
            } else {
                return response()->json($message, 500);
            }

        }
    }

    public function update(Request $request, $id)
    {
        $fields = $request->all();

        $rules    = [
            'family_name' => 'required',
            'given_name'  => 'required',
            'email'       => 'required',
        ];
        $messages = [
            'family_name.required' => 'Family name is required.',
            'given_name.required'  => 'Given name is required.',
            'email.required'       => 'Email address is required.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors, 400);
        } else {
            $message   = new StdClass();
            $testtaker = new Testtaker();
            DB::beginTransaction();

            try {
                $testtaker = Testtaker::findOrFail($id);

                $testtaker->given_name     = $fields['given_name'] ?? '';
                $testtaker->family_name    = $fields['family_name'] ?? '';
                $testtaker->suffix         = $fields['suffix'] ?? '';
                $testtaker->nickname       = $fields['nickname'] ?? '';
                $testtaker->nice_name      = $fields['nice_name'] ?? ($fields['nickname'] . ' ' . $fields['family_name']);
                $testtaker->email          = $fields['email'] ?? '';
                $testtaker->phone_mobile   = $fields['phone_mobile'] ?? '';
                $testtaker->phone_landline = $fields['phone_landline'] ?? '';

                $testtaker->school      = $fields['school'] ?? '';
                $testtaker->address     = $fields['address'] ?? '';
                $testtaker->locality    = $fields['locality'] ?? '';
                $testtaker->county      = $fields['county'] ?? '';
                $testtaker->state       = $fields['state'] ?? '';
                $testtaker->country_id  = $fields['country']['id'] ?? '';
                $testtaker->postal_code = $fields['postal_code'] ?? '';

                $testtaker->save();

                $role = Setting::find('testtaker.role')->value;

                /** @var User $user */
                $user = User::ofRole($role)->where('id', $testtaker->user_id)->first();

                $user->name = $fields['user']['name'] ?? '';

                if (isset($fields['user']['password']) && !empty($fields['user']['password'])) {
                    $user->password = trim($fields['user']['password']) != '' ? Hash::make(trim($fields['user']['password'])) : Hash::make('password');
                }

                $user->email     = $fields['email'] ?? $user->email;
                $user->nice_name = $fields['nice_name'] ?? $user->nice_name;

                $user->save();

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
                return response()->json($testtaker, 200);
            } else {
                return response()->json($message, 500);
            }

        }
    }

    public function updateMultiple(Request $request)
    {

    }

    public function delete(Request $request, $id)
    {
        unset($request);

        $message = new StdClass();

        /** @var Testtaker $entity */
        $testtaker = Testtaker::findOrFail($id);

        DB::beginTransaction();

        try {
            Testtaker::destroy($id);
            if (!empty($testtaker->user_id)) {
                User::where('id', $testtaker->user_id)->delete();
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

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Testtaker::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

}
