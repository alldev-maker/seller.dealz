<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Roster\Testtaker;
use App\Models\Settings\Setting;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass as StdClass;


class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.register.index');
    }

    public function done()
    {
        return view('auth.register.done');
    }

    public function create(Request $request)
    {
        $fields = $request->all();

        $rules    = [
            'family_name'           => 'required',
            'given_name'            => 'required',
            'email'                 => 'required',
            'user.password'         => ['required', 'string', 'min:10', 'required_with:user.password_confirm', 'same:user.password_confirm'],
            'user.password_confirm' => ['required', 'string', 'min:10'],
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
                $testtaker->nickname       = isset($fields['nickname']) ? ($fields['nickname'] ?? $fields['given_name']) : $fields['given_name'];
                $testtaker->nice_name      = $fields['nice_name'] ?? ($testtaker->nickname . ' ' . $testtaker->family_name);
                $testtaker->email          = $fields['email'] ?? '';
                $testtaker->phone_mobile   = $fields['phone_mobile'] ?? '';
                $testtaker->phone_landline = $fields['phone_landline'] ?? '';

                $testtaker->school      = $fields['school'] ?? '';
                $testtaker->address     = $fields['address'] ?? '';
                $testtaker->locality    = $fields['locality'] ?? '';
                $testtaker->county      = $fields['county'] ?? '';
                $testtaker->state       = $fields['state'] ?? '';
                $testtaker->county      = $fields['country']['id'] ?? '';
                $testtaker->postal_code = $fields['postal_code'] ?? '';

                $testtaker->save();

                $user = new User();

                $user->id        = $user->generateId();
                $user->name      = $fields['user']['name'] ?? '';
                $user->password  = Hash::make(trim($fields['user']['password']));
                $user->email     = $testtaker->email;
                $user->role_id   = Setting::find('testtaker.role')->value->id;
                $user->nice_name = $testtaker->nice_name;

                $user->save();

                $testtaker->user_id = $user->id;

                $testtaker->update();

                DB::commit();

                event(new Registered($user));

                $this->guard()->login($user);

                $message->status  = 'success';
                $message->content = $this->name['singular'] . ' has been created.';

            } catch (Exception $e) {
                DB::rollback();

                $message->status  = 'danger';
                $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
                $message->reason  = $e->getMessage();
            }

            if ($message->status === 'success') {
                return response()->json($testtaker, 201);
            } else {
                return response()->json($message, 500);
            }
        }
    }
}
