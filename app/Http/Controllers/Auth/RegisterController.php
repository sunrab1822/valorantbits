<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $images = Storage::disk("public")->allFiles("profile_images");
        $image_key = array_rand($images);

        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_image' => "/storage/" . $images[$image_key]
        ]);
    }

    public function register(Request $request) {
        try {
            $this->validate($request, [
                'username' => 'required|max:32|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'password_confirmation' => 'required'
            ]);
        } catch(ValidationException $ex) {
            return json_encode(["error" => true, "error_type" => "validation", "data" => $ex->errors()]);
        }

        $images = Storage::disk("public")->allFiles("profile_images");
        $image_key = array_rand($images);

        User::create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'profile_image' => "/storage/" . $images[$image_key]
        ]);

        if(Auth::attempt(["username" => $request->get("username"), "password" => $request->get("password")])) {
            return json_encode(["error" => false, "data" => Auth::user()]);
        }

        return json_encode(["error" => false, "data" => null]);
    }
}
