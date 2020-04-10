<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {

        $dataLogin = $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($dataLogin)) {
            $user = Auth::user()->createToken(env('APP_NAME',  'Laravel'));

            return response()->json([
                'success' => [
                    'token' => $user->accessToken
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthorised'
            ], 401);
        }
    }
}
