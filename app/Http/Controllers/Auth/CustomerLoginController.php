<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        // $this->middleware('guest:customer')->except('logout');
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
        Auth::guard('customer')->logout();
        return redirect('/login');
    }


    public function customerLogin(Request $request)
    { 
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('customer')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->to('/customer/home');
        }
        return back()->withInput($request->only('username', 'remember'));
    }
}
