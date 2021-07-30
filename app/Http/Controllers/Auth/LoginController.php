<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Sets the field name of username.
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Renders the login page.
     *
     * @return View
     */
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $isSuccess = $this->attemptLogin($request);
        $isAjax    = $request->ajax();

        if ($isSuccess) {
            if ($isAjax) {
                return response()->json(
                    [
                        'result' => 1,
                    ]
                );
            } else {
                return redirect()->intended('home');
            }
        } else {
            if ($isAjax) {
                return response()->json(
                    [
                        'result' => 0,
                    ]
                );
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function logout(Request $request)
    {
        $isAjax = $request->ajax();

        Auth::logout();
        $request->session()->invalidate();


        if ($isAjax) {
            return response()->json(
                [
                    'result' => 1,
                ]
            );
        } else {
            return redirect()->route('home');
        }
    }

    protected function credentials(Request $request)
    {
        $data = $request->only('username', 'password');

        return [
            'name'     => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::guard()->attempt($this->credentials($request), $request->filled('remember'));
    }
}
