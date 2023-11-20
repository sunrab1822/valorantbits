<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use function Symfony\Component\String\b;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username() {
        return "username";
    }

    public function login(Request $request) {
        try {
            $this->validate($request, [
                'username' => 'required|exists:users',
                'password' => 'required'
            ]);
        } catch (ValidationException $ex) {
            return json_encode(["error" => true, "error_type" => "validation", "data" => $ex->errors()]);
        }


        if(Auth::attempt(["username" => $request->get("username"), "password" => $request->get("password")], $request->get("remember"))) {
            return json_encode(["error" => false, "data" => Auth::user()]);
        }

        return json_encode(["error" => true, "data" => false]);
    }

    public function logout() {
        if(Auth::check()) {
            Auth::logout();
            return json_encode(["error" => false, "data" => false]);
        }

        return json_encode(["error" => true, "data" => "You are not logged in"]);
    }
}
