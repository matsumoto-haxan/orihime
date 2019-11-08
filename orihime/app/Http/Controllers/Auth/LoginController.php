<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * ログイン時の処理（オーバーライド）
     */
    protected function authenticated(Request $request, $user)
    {
        $user->update(['api_token' => str_random(60)]);
    }

    /**
     * ログアウト時の処理（オーバーライド）
     */
    public function logout(Request $request)
    {
        // api_tokenをnullにする
        $user = $request->user();
        $user->update(['api_token' => null]);
    
        $this->guard()->logout();
    
        $request->session()->flush();
        $request->session()->regenerate();
    
        return redirect('/');
    }
}
