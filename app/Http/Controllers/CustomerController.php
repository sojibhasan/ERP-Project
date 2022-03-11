<?php

namespace App\Http\Controllers;

use App\BusinessCategory;
use App\BusinessLocation;
use App\Currency;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use App\Charts\CommonChart;
use App\Media;
use App\System;

class CustomerController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact_id = Auth()->user()->username;

        $fy = $this->businessUtil->getCurrentFinancialYear(1);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', 1)->first();

        // //Chart for purhcase last 30 days
        $purhcase_last_30_days = $this->transactionUtil->getPurchaseLast30DaysForCustomer($contact_id);
        $labels = [];
        $all_purchase_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($purhcase_last_30_days[$date])) {
                $all_purchase_values[] = $purhcase_last_30_days[$date];
            } else {
                $all_purchase_values[] = 0;
            }
        }

        $pruchase_chart_1 = new CommonChart;
        $pruchase_chart_1->labels($labels)
            ->options($this->__chartOptions(__(
                'ustomer.total_purhcase',
                ['currency' => $currency->code]
            )));


        $pruchase_chart_1->dataset('customer.total_purhcase', 'line', $all_purchase_values);

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        return view('ecom_customer.home.index', compact('date_filters', 'pruchase_chart_1', 'widgets'));
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email',
            'username' => 'required|unique:customers',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'mobile' => 'required|numeric',
            'contact_number' => 'numeric',
            'landline' => 'numeric',
            'geo_location' => 'required',
            'address' => 'required',
            'town' => 'required',
            'district' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }
        try {
            $customer_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'mobile' => $request->mobile,
                'contact_number' => $request->contact_number,
                'landline' => $request->landline,
                'geo_location' => $request->geo_location,
                'address' => $request->address,
                'town' => $request->town,
                'district' => $request->district,
                'give_away_gifts' => $request->give_away_gifts
            ];
            DB::beginTransaction();
            $customer = Customer::create($customer_data);

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_customer_created', ['customer' => $customer]);
            }

            $this->businessUtil->addReferral($request->referral_code, 'customer', $customer->id, null);

            DB::commit();

            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            $title = System::getProperty('customer_register_success_title');
            $msg = System::getProperty('customer_register_success_msg');
            $msg = str_replace('{username}', $customer->username, $msg);
            $msg = str_replace('{first_name}', $customer->first_name, $msg);
            $msg = str_replace('{last_name}', $customer->last_name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);

            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            return redirect()->back()->with('register_success', $output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with('status', $output);
        }
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
     * Get Home dashboard totals
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        $contact_id = Auth()->user()->username;
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;

            $purchase_details = $this->transactionUtil->getPurchaseTotalsForCustomer($contact_id, $start, $end);

            $output['total_purchases'] = $purchase_details['total_purchase_inc_tax'];
            $output['purchase_dues'] = $purchase_details['purchase_dues'];
            $output['total_paids'] = $purchase_details['total_paids'];

            return $output;
        }
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title
                ]
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }

    /**
     * Shows profile of logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $customer_id = Auth::user()->id;
        $customer = Customer::where('id', $customer_id)->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $dues = $this->transactionUtil->getGeneralCustomerDue($customer_id);

        return view('ecom_customer.profile.index', compact('customer', 'languages', 'dues'));
    }

    /**
     * updates user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $customer_id = Auth::user()->id;
            $input = $request->only([
                'surname',
                'first_name',
                'last_name',
                'email',
                'mobile',
                'contact_number',
                'landline',
                'geo_location',
                'address',
                'town',
                'towndistrict',
            ]);
            $input['business_id'] = 0;
            $customer = Customer::find($customer_id);
            $customer->update($input);

            Media::uploadMedia($customer->business_id, $customer, request(), 'profile_photo', true);

            //update session
            $input['id'] = $customer_id;
            $business_id = request()->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            session()->put('user', $input);

            $output = [
                'success' => 1,
                'msg' => 'Profile updated successfully'
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Something went wrong, please try again'
            ];
        }
        return redirect('customer/profile')->with('status', $output);
    }

    /**
     * updates user password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $customer_id = Auth::user()->id;
            $customer = Customer::where('id', $customer_id)->first();

            if (Hash::check($request->input('current_password'), $customer->password)) {
                $customer->password = Hash::make($request->input('new_password'));
                $customer->save();
                $output = [
                    'success' => 1,
                    'msg' => 'Password updated successfully'
                ];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => 'You have entered wrong password'
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Something went wrong, please try again'
            ];
        }
        return redirect('customer/profile')->with('status', $output);
    }
}
