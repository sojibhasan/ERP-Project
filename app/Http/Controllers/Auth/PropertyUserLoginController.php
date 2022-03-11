<?php

namespace App\Http\Controllers\Auth;

use App\Business;
use App\Http\Controllers\Controller;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Litespeed\LSCache\LSCache;

class PropertyUserLoginController extends Controller
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

        if ($business->id != 1) {
            if (!$this->moduleUtil->hasThePermissionInSubscription($business->id, 'property_module')) {
                abort(403, 'Unauthorized action.');
            }
        }

        if (Auth::user() && request()->session()->get('user.is_pump_operator')) {
            return redirect()->to('/property/sale_and_customer_payment/index');
        }
        return view('property::user.login')->with(compact(
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
        $business_id = $request->business_id;
        $passcode = $request->passcode;
        $users = User::where('business_id', $business_id)->select('id', 'password')->get();
       
        foreach ($users as $u) {
            if (Hash::check($passcode, $u->password)) {
                $user = $u;
                break;
            }
        }

        if (!empty($user)) {
            Auth::loginUsingId($user->id);

            return redirect()->to('/property/sale-and-customer-payment/dashboard');
        } else {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.sorry_user_not_found')
            ];

            return redirect()->back()->with('status', $output);
        }


        return view('petro::pump_operators.login')->with(compact(
            'business',
            'settings'
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
        return redirect('property-user/login?cc=' . $cc);
    }
}
