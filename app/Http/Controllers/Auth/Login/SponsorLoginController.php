<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController as DefaultLoginController;
use Illuminate\Support\Facades\Session;

class SponsorLoginController extends DefaultLoginController
{
    
    protected $redirectTo = '/sponsor/home';


    public function __construct()
    {

        $this->middleware('guest:sponsor')->except('logout');
       
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function username()
    {
        return 'email';
    }

    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);
        $session=$request->email;
        $remember_me = $request->has('remember') ? true : false; 

        if(auth()->guard('sponsor')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ],$remember_me)) {
            return redirect()->intended(url('my-account'));
        } else {
            return redirect()->back()->with(['error' => 'Email atau password yang anda masukan salah.']);
        }
    }
    
    protected function guard()
    {
        return Auth::guard('sponsor');
    }

    public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
}

}
