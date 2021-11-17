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
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $session=$request->email;
               
        if(auth()->guard('sponsor')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $user = auth()->user();

            $data['session']=session(['key' => $session]);

            return redirect()->intended(url('my-account',$data));
        } else {
            return redirect()->back()->withError('Credentials doesn\'t match.');
        }
    }
    
    protected function guard()
    {
        return Auth::guard('sponsor');
    }


}
