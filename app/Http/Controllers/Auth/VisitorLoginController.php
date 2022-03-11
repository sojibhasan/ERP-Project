<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorLoginController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        // $this->middleware('guest:member')->except('logout');
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        request()->session()->flush();
        Auth::guard('visitor')->logout();
        return redirect('/login');
    }


    public function visitorLogin(Request $request)
    { 
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('visitor')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->to('/visitor/home');
        }
        return back()->withInput($request->only('username', 'remember'));
    }
}
