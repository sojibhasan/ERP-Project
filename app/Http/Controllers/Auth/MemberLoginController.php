<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MemberLoginController extends Controller
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
        Auth::guard('member')->logout();
        return redirect('/login');
    }


    public function memberLogin(Request $request)
    { 
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('member')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->to('/member/home');
        }
        return back()->withInput($request->only('username', 'remember'));
    }
}
