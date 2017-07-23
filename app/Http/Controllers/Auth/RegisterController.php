<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Adldap;

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
    protected $redirectTo = '/home';

    protected $schema;

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
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showRegistrationForm()
    {
        return view('custom-auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $adminUsers = 'ou=' . env('ADLDAP_ADMIN_USRES_OU') . ',' . env('ADLDAP_BASEDN');
        $users = 'ou=' . env('ADLDAP_USRES_OU') . ',' . env('ADLDAP_BASEDN');
        $ou = null;

        if(($ou = Adldap::search()->findByDn($adminUsers)) == null) {
            $ou = Adldap::make()->entry([
                'ou' => env('ADLDAP_ADMIN_USRES_OU'),
                'objectclass' => ['top', 'organizationalunit'],
                'description' => env('ADLDAP_ADMIN_USRES_OU_DES')
            ]);
            $ou->setDn($adminUsers);
            $ou->save();

        } else if(($ou = Adldap::search()->findByDn($users)) == null) {
            $ou = Adldap::make()->entry([
                'ou' => env('ADLDAP_USRES_OU'),
                'objectclass' => ['top', 'organizationalunit'],
                'description' => env('ADLDAP_USRES_OU_DES')
            ]);
            $ou->setDn($users);
            $ou->save();
        }

        $user = Adldap::make()->entry([
            'uid' => $request->input('username'),
            'uidnumber' => 1000,
            'gidnumber' => 0,
            'cn' => $request->input('username'),
            'sn' => $request->input('username'),
            'uid' => $request->input('username'),
            'homedirectory' => $ou->getDn(),
            'userpassword' => bcrypt($request->input('password')),
            'mail' => $request->input('email'),
            'objectclass' => ['top', 'posixaccount', 'inetorgperson'],
        ]);

        $dn = 'uid=' . $request->input('username') . ',' . $ou->getDn();
        $user->setDn($dn);
        if(Adldap::search()->findByDn($dn) == null) {
            $user->save();
        } else {
            back();
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
}
