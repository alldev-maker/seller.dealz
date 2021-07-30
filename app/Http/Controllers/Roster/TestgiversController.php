<?php

namespace App\Http\Controllers\Roster;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Roster\Testgiver;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass as StdClass;

class TestgiversController extends Controller
{
    public function __construct()
    {
        $this->name = [
            'singular' => 'Test Giver',
            'plural'   => 'Test Givers',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Testgiver::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $testgiver = $id == '' ? new Testgiver() : Testgiver::findOrFail($id);

        if ($id == '') {
            $testgiver->id          = '';
            $testgiver->email       = '';
            $testgiver->family_name = '';
            $testgiver->given_name  = '';
            $testgiver->suffix      = '';

            $testgiver->user = new User();
        }

        return view(sprintf('%s.form', Testgiver::PATH), ['testgiver' => $testgiver]);
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
            $testgivers = Testgiver::whereRaw($qry, $prm)->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $testgivers = Testgiver::paginate($l, ['*'], 'p')->appends($appends);
        }

        unset($p);

        return $this->render($testgivers);
    }

    public function show($id)
    {
        return Testgiver::findOrFail($id);
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
            $testgiver = new Testgiver();
            DB::beginTransaction();

            try {
                $testgiver = new Testgiver();

                $testgiver->id             = $testgiver->generateId();
                $testgiver->given_name     = $fields['given_name'] ?? '';
                $testgiver->family_name    = $fields['family_name'] ?? '';
                $testgiver->suffix         = $fields['suffix'] ?? '';
                $testgiver->nickname       = $fields['nickname'] ?? '';
                $testgiver->nice_name      = $fields['nice_name'] ?? ($fields['nickname'] . ' ' . $fields['family_name']);
                $testgiver->email          = $fields['email'] ?? '';
                $testgiver->phone_mobile   = $fields['phone_mobile'] ?? '';
                $testgiver->phone_landline = $fields['phone_landline'] ?? '';

                $testgiver->school      = $fields['school'] ?? '';
                $testgiver->address     = $fields['address'] ?? '';
                $testgiver->locality    = $fields['locality'] ?? '';
                $testgiver->county      = $fields['county'] ?? '';
                $testgiver->state       = $fields['state'] ?? '';
                $testgiver->country_id  = $fields['country']['id'] ?? '';
                $testgiver->postal_code = $fields['postal_code'] ?? '';

                $testgiver->save();

                $user = new User();

                $user->id       = $user->generateId();
                $user->name     = $fields['user']['name'] ?? '';
                $user->password = trim($fields['user']['password']) != '' ? Hash::make(trim($fields['user']['password'])) : Hash::make('password');
                $user->email    = $fields['user']['email'] ?? '';
                $user->role_id  = $fields['role']['id'] ?? Setting::find('testgiver.role')->value->id;

                $user->save();

                $testgiver->user_id = $user->id;

                $testgiver->update();

                DB::commit();

                $message->status  = 'success';
                $message->content = $this->name['singular'] . ' has been created.';

            } catch (\Exception $e) {
                DB::rollback();

                $message->status  = 'danger';
                $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
                $message->reason  = $e->getMessage();
            }

            $request->session()->put('message', $message);

            if ($message->status === 'success') {
                return response()->json($testgiver, 201);
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
            $testgiver = new Testgiver();
            DB::beginTransaction();

            try {
                $testgiver = Testgiver::findOrFail($id);

                $testgiver->given_name     = $fields['given_name'] ?? '';
                $testgiver->family_name    = $fields['family_name'] ?? '';
                $testgiver->suffix         = $fields['suffix'] ?? '';
                $testgiver->nickname       = $fields['nickname'] ?? '';
                $testgiver->nice_name      = $fields['nice_name'] ?? ($fields['nickname'] . ' ' . $fields['family_name']);
                $testgiver->email          = $fields['email'] ?? '';
                $testgiver->phone_mobile   = $fields['phone_mobile'] ?? '';
                $testgiver->phone_landline = $fields['phone_landline'] ?? '';

                $testgiver->school      = $fields['school'] ?? '';
                $testgiver->address     = $fields['address'] ?? '';
                $testgiver->locality    = $fields['locality'] ?? '';
                $testgiver->county      = $fields['county'] ?? '';
                $testgiver->state       = $fields['state'] ?? '';
                $testgiver->country_id  = $fields['country']['id'] ?? '';
                $testgiver->postal_code = $fields['postal_code'] ?? '';

                $testgiver->save();

                $role = Setting::find('testgiver.role')->value;

                /** @var User $user */
                $user = User::ofRole($role)->where('id', $testgiver->user_id)->first();

                $user->name = $fields['user']['name'] ?? '';

                if (isset($fields['user']['password']) && !empty($fields['user']['password'])) {
                    $user->password = trim($fields['user']['password']) != '' ? Hash::make(trim($fields['user']['password'])) : Hash::make('password');
                }

                $user->email     = $fields['user']['email'] ?? $user->email;
                $user->nice_name = $fields['nice_name'] ?? $user->nice_name;

                $user->save();

                DB::commit();

                $message->status  = 'success';
                $message->content = $this->name['singular'] . ' has been updated.';

            } catch (\Exception $e) {
                DB::rollback();

                $message->status  = 'danger';
                $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
                $message->reason  = $e->getMessage();
            }

            $request->session()->put('message', $message);

            if ($message->status === 'success') {
                return response()->json($testgiver, 200);
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

        /** @var Testgiver $entity */
        $testgiver = Testgiver::findOrFail($id);

        DB::beginTransaction();

        try {
            Testgiver::destroy($id);
            if (!empty($testgiver->user_id)) {
                User::where('id', $testgiver->user_id)->delete();
            }

            $message->status = 'success';

            DB::commit();

        } catch (\Exception $e) {
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

        Testgiver::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

}
