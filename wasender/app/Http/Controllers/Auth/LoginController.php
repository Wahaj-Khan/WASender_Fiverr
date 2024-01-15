<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->hasVerifiedEmail()) {
            $this->guard()->logout();
            return redirect('/login')->with('warning', 'You need to verify your email address before logging in.');
        }

        if ($user->role == 'user') {
            return redirect('/user/dashboard');
        } elseif ($user->role == 'admin') {
            return redirect('/admin/dashboard');
        }

        return redirect('/');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();

            if ($user->hasVerifiedEmail()) {
                return $this->sendLoginResponse($request);
            } else {
                $this->guard()->logout();
                return $this->sendFailedLoginResponse($request, 'You need to verify your email address before logging in.');
            }
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function username()
    {
        return 'email';
    }
}
