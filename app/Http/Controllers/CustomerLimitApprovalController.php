<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Events\CustomerLimitApproval;
use App\Events\CustomerLimitApproved;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerLimitApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * send request for approval
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendRequestForApproval($customer_id)
    {
        $business_id = request()->session()->get('business.id');
        $users = User::where('business_id', $business_id)->permission('approve_sell_over_limit')->get();
        $customer = Contact::findOrFail($customer_id);
        foreach ($users as $user) {
            event(new CustomerLimitApproval($user->id, $customer_id, $customer->name));
        }

        return ['success' => 1, 'msg' => __('lang_v1.request_sent_success')];
    }

    /**
     * send request for approval
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getApprovalDetails($customer_id, $requested_user)
    {
        $business_id = request()->session()->get('business.id');
        $customer = Contact::findOrFail($customer_id);
        Contact::where('id', $customer_id)->update(['temp_requested_by' => $requested_user]);
        
        return view('customer_settings.limit_modal')->with(compact('customer', 'requested_user'));
    }

    /**
     * send request for approval
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateApprovalLimit($customer_id)
    {
        try {

            Contact::where('id', $customer_id)->update(['over_limit_percentage' => request()->over_limit_percentage, 'temp_approved_user' => Auth::user()->id]);

            $customer = Contact::findOrFail($customer_id);
            event(new CustomerLimitApproved($customer_id,  request()->requested_user,  $customer->name,  request()->over_limit_percentage));

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.limit_approved_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
