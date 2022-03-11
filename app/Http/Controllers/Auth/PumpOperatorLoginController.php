<?php

namespace App\Http\Controllers\Auth;

use App\Business;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Litespeed\LSCache\LSCache;

class PumpOperatorLoginController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ModuleUtil $moduleUtil
    ) {
        $this->moduleUtil = $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function login()
    {
        $settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
        $cc = request()->cc;
        $business = null;
        if (!empty($cc)) {
            $business = Business::where('company_number', $cc)->select('name', 'id')->first();
        } else {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user() && request()->session()->get('user.is_pump_operator')) {
            return redirect()->to('/petro/pump-operators/dashboard');
        }
        return view('petro::pump_operators.login')->with(compact(
            'settings',
            'business'
        ));
    }

    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }

        $business_id = request()->session()->get('business.id');
        $passcode = $request->passcode;
        $user = User::where('pump_operator_passcode', $passcode)->first();

        if (!empty($user)) {
            Auth::loginUsingId($user->id);

            return redirect()->to('/petro/pump-operators/dashboard');
        } else {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.sorry_user_not_found')
            ];

            return redirect()->back()->with('status', $output);
        }


        return view('petro::pump_operators.login')->with(compact(
            'business',
            'settings',
        ));
    }

    /**
     * logout pump operator
     * @return Renderable
     */
    public function logout(Request $request)
    {
        $cc = $request->session()->get('business.company_number');
        request()->session()->flush();
        LSCache::purge('*');
        Auth::logout();
        if($request->main_system){
            return redirect('/login?cc=' . $cc);
        }

        return redirect('/pump-operator/login?cc=' . $cc);
    }
}
