<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentLoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        // $this->middleware('guest:agent')->except('logout');
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
        Auth::guard('agent')->logout();
        return redirect('/login');
    }


    public function agentLogin(Request $request)
    { 
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('agent')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->to('/agent');
        }
        return back()->withInput($request->only('username', 'remember'));
    }
}
