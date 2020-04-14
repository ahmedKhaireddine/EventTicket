<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
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

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $attributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $attributes)
    {
        return Validator::make($attributes, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:255'],
            'job' => ['required', 'string'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $attributes
     * @return \App\User
     */
    protected function create(array $attributes)
    {
        $user = new User();
        $user->email = $attributes['email'];
        $user->first_name = $attributes['first_name'];
        $user->job = $attributes['job'];
        $user->last_name = $attributes['last_name'];
        $user->password = $attributes['password'];
        $user->phone = $attributes['phone'];
        $user->role = 'admin';
        $user->save();

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\UserResource
     */
    public function register(Request $request)
    {
        $attributes = $this->validator($request->all())->validate();

        $user = $this->create($attributes);

        return new UserResource($user);
    }
}
