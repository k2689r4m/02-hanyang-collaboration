<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\MyPage;
use App\Models\Card;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
//        ^
//        (?=[^a-z\n]*[a-z]) # ensure one lower case letter
//        (?=[^A-Z\n]*[A-Z]) # ensure one upper case letter
//        (?=[^\d\n]*\d)     # ensure a digit
//        (?=[^!@?\n]*[!@?]) # special chars
//        .{6,12}            # at least 6 characters long, max 12 characters long
//        $

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'max:255', 'unique:users', 'email'],//'min:4', 'string'],
            'password' => ['required', 'string', 'regex:/^(?=[^a-z\n]*[a-z])(?=[^\d\n]*\d).{6,12}$/', 'confirmed'],
            'contact' => ['required'],//, 'regex:/(010)[0-9]{8}/'],
            'code' => ['string', 'nullable'],//['regex:/^(?=[^a-z\n]*[a-z])(?=[^\d\n]*\d).{8,8}$/', 'nullable'],
            'check1' => ['required'],//, 'boolean', 'in:1,true'],
            'check2' => ['required'],//, 'boolean', 'in:1,true']
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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'contact' => $data['contact'],
            'code' => $data['code'],
            'authority' => is_null($data['code']) ? '1' : '5',
        ]);

        if (in_array($user->authority, [1, 2, 5])) {
            $myPage = MyPage::where('userId', $user->id)->first();
            if (!$myPage) {
                MyPage::create([
                    'userId' => $user->id,
                ]);
            }
        }

        return $user;
    }
}
