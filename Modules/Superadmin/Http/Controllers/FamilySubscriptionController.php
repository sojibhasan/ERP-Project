<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Currency;
use App\PackageVariable;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\FamilySubscription;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Support\Str;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;
use \Notification;
use Yajra\DataTables\Facades\DataTables;

class FamilySubscriptionController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse('patient');
        }
        $subscription = Subscription::active_subscription($business_id);
        if (!empty($subscription)) {
            $package = Package::find($subscription->package_id);
        } else {
            $package = null;
        }

        if (request()->ajax()) {
            $superadmin_subscription = FamilySubscription::join('business', 'family_subscriptions.business_id', '=', 'business.id')
                ->join('packages', 'family_subscriptions.package_id', '=', 'packages.id')
                ->join('users', 'business.id', '=', 'users.business_id')
                ->select('users.username as patient_code', 'packages.name as package_name', 'family_subscriptions.status', 'family_subscriptions.no_of_family_members',  'family_subscriptions.amount_to_pay', 'family_subscriptions.paid_via', 'family_subscriptions.payment_transaction_id', 'family_subscriptions.id', 'packages.currency_id', 'business.is_patient')
                ->groupBy('family_subscriptions.id');

            return DataTables::of($superadmin_subscription)
                ->addColumn(
                    'action',
                    '<button data-href ="{{action(\'\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@edit\',[$id])}}" class="btn btn-info btn-xs change_status" data-toggle="modal" data-target="#statusModal">
                            @lang( "superadmin::lang.status")'
                )
                ->editColumn(
                    'status',
                    '@if($status == "approved")
                                <span class="label bg-light-green">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @elseif($status == "waiting")
                                <span class="label bg-aqua">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @else($status == "declined")
                                <span class="label bg-red">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @endif'
                )
                ->editColumn(
                    'no_of_family_members',
                    '{{@num_format($no_of_family_members)}}'
                )
                ->editColumn(
                    'package_price',
                    function ($row) {
                        $currency_symbol = Currency::where('id', $row->currency_id)->first()->symbol;
                        $html = $currency_symbol . ' ' . $row->amount_to_pay;
                        return $html;
                    }
                )
                ->editColumn(
                    'patient_code',
                    function ($row) {
                        if ($row->is_patient) {
                            $html = $row->patient_code;
                        } else {
                            $html = '';
                        }


                        return $html;
                    }
                )
                ->removeColumn('currency_id')
                ->removeColumn('is_patient')
                ->removeColumn('id')
                ->rawColumns([2, 7])
                ->make(false);
        }

        $package_period = 0;
        $balance_period = 0;
        if (!empty($package)) {
            $package_period = Package::getPackagePeriodInDays($package);
            $balance_period = Subscription::remaning_days($business_id);
        }

        $order_id = Str::random(5);
        return view('patient.family_subscription')->with(compact(
            'package',
            'order_id',
            'package_period',
            'balance_period'
        ));
    }


    public function getPatientSubscriptions()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $family_subscription = FamilySubscription::join('business', 'family_subscriptions.business_id', '=', 'business.id')
                ->join('packages', 'family_subscriptions.package_id', '=', 'packages.id')
                ->join('users', 'business.id', '=', 'users.business_id')
                ->where('family_subscriptions.business_id',  $business_id)
                ->select('users.username as patient_code', 'packages.name as package_name', 'family_subscriptions.status', 'family_subscriptions.no_of_family_members',  'family_subscriptions.amount_to_pay', 'family_subscriptions.paid_via', 'family_subscriptions.payment_transaction_id', 'family_subscriptions.id', 'packages.currency_id', 'business.is_patient')
                ->groupBy('family_subscriptions.id');

            return DataTables::of($family_subscription)
                ->editColumn(
                    'status',
                    '@if($status == "approved")
                                <span class="label bg-light-green">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @elseif($status == "waiting")
                                <span class="label bg-aqua">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @else($status == "declined")
                                <span class="label bg-red">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @endif'
                )
                ->editColumn(
                    'no_of_family_members',
                    '{{@num_format($no_of_family_members)}}'
                )
                ->editColumn(
                    'package_price',
                    function ($row) {
                        $currency_symbol = Currency::where('id', $row->currency_id)->first()->symbol;
                        $html = $currency_symbol . ' ' . $row->amount_to_pay;
                        return $html;
                    }
                )
                ->editColumn(
                    'patient_code',
                    function ($row) {
                        if ($row->is_patient) {
                            $html = $row->patient_code;
                        } else {
                            $html = '';
                        }


                        return $html;
                    }
                )
                ->removeColumn('currency_id')
                ->removeColumn('is_patient')
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('superadmin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $family_subscription = FamilySubscription::where('business_id', $business_id)->where('order_id', $request->order_id)->first();
        if (empty($family_subscription)) {
            $input = $request->only(['package_id', 'no_of_family_members', 'option_variable_id', 'order_id']);
            $input['business_id'] = $business_id;
            $input['amount_to_pay'] = $request->amount_to_pay_hidden;
            $input['status'] = 'waiting';
            $input['created_by'] = Auth::user()->id;


            $family_subscription = FamilySubscription::create($input);
        }

        $package = Package::find($request->package_id);
        $no_of_family_members = $request->no_of_family_members;
        $amount_to_pay  = $request->amount_to_pay_hidden;

        $gateways = $this->_payment_gateways();
        $order_id = $request->order_id;
        $currency_code = !empty($package->currency_id) ? Currency::where('id', $package->currency_id)->first()->code : 'LKR';
        $layout = 'layouts.app';

        return view('patient.pay')->with(compact(
            'package',
            'no_of_family_members',
            'amount_to_pay',
            'gateways',
            'order_id',
            'currency_code',
            'family_subscription',
            'layout'
        ));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $status = Subscription::package_subscription_status();
            $subscription = FamilySubscription::find($id);

            return view('superadmin::superadmin_subscription.edit_family_subscription')
                ->with(compact('subscription', 'status'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $family_subscriptions = FamilySubscription::findOrFail($id);
                $business_id = $family_subscriptions->business_id;
                $input = $request->only(['status', 'payment_transaction_id']);

                $family_subscriptions->status = $input['status'];
                $family_subscriptions->payment_transaction_id = $input['payment_transaction_id'];
                $family_subscriptions->save();

                if ($input['status'] == 'approved') {
                    $active = Subscription::active_subscription($business_id);
                    $package_details = $active->package_details;
                    $package_details['user_count'] = $package_details['user_count'] + $family_subscriptions->no_of_family_members;

                    $active->package_details = $package_details;
                    $active->save();
                }
                $output = array(
                    'success' => true,
                    'msg' => __("superadmin::lang.subcription_updated_success")
                );
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = array(
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                );
            }
            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    /**
     * confirm subscription
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $family_subscription = FamilySubscription::where('business_id', $business_id)->where('order_id', $request->order_id)->first();
        $family_subscription->paid_via = $request->gateway;
        $family_subscription->save();
        $order_id = $request->order_id;
        if (request()->gateway == 'payhere') {
            $payhere_data = DB::table('payhere')->where('order_id', $order_id)->where('business_id', $business_id)->first();
            $initial_data = array(
                'order_id' => $order_id,
                'business_id' => $business_id,
                'package_id' => null,
                'transaction_id' => null,
                'user_id' => auth()->user()->id,
                'price' => $family_subscription->amount_to_pay,
                'status' => 'pending',
            );
            if (empty($payhere_data)) {
                DB::table('payhere')->insert($initial_data);
            } else {
                DB::table('payhere')->where('id', $payhere_data->id)->update($initial_data);
            }
        }
        $msg = __('lang_v1.success');

        if (request()->ajax()) {
            $output = ['success' => 1, 'msg' => $msg];
            return $output;
        }

        if (request()->gateway == 'offline') {
            $msg = __('superadmin::lang.notification_sent_for_approval');
            $output = ['success' => 1, 'msg' => $msg];
        }

        return redirect('superadmin/family-subscription')->with('status', $output);
    }

    /**
     * confirm subscription
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function notifyPayhere(Request $request)
    {
        $merchant_id         = $request->merchant_id;
        $order_id             = $request->order_id;
        $payhere_amount     = $request->payhere_amount;
        $payhere_currency    = $request->payhere_currency;
        $status_code         = $request->status_code;
        $md5sig                = $request->md5sig;

        $merchant_secret = env('PAYHERE_MERCHANT_SECRET'); // Replace with your Merchant Secret (Can be found on your PayHere account's Settings page)

        $local_md5sig = strtoupper(md5($merchant_id . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($merchant_secret))));

        if (($local_md5sig === $md5sig) and ($status_code == 2)) {
            //TODO: Update your database as payment success
            $payment_data = DB::table('payhere')->where('order_id', $order_id)->first();
            $payment_transaction_id = $payment_data->id;

            $business_id = $payment_data->business_id;

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'completed';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);

            $family_subscription = FamilySubscription::where('business_id', $business_id)->where('order_id', $order_id)->first();
            if (!empty($family_subscription)) {
                $family_subscription->status = 'approved';
                $family_subscription->payment_transaction_id = $payment_transaction_id;
                $family_subscription->save();
            }

            //update family member count
            $active = Subscription::active_subscription($business_id);
            $package_details = $active->package_details;
            $package_details['user_count'] = $package_details['user_count'] + $family_subscription->no_of_family_members;

            $active->package_details = $package_details;
            $active->save();

            $email = System::getProperty('email');
            $is_notif_enabled = System::getProperty('enable_new_subscription_notification');

            //send package added email notification to user
            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                    ->notify(new NewSubscriptionNotification($family_subscription));
            }


            $family_subscription->save();
        } else {
            $payment_data = DB::table('payhere')->where('order_id', $order_id)->first();
            $payment_transaction_id = $payment_data->id;
            $business_id = $payment_data->business_id;

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'declined';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);


            $family_subscription = FamilySubscription::where('business_id', $business_id)->where('order_id', $order_id)->first();
            if (!empty($family_subscription)) {
                $family_subscription->status = 'declined';
                $family_subscription->payment_transaction_id = $payment_transaction_id;
                $family_subscription->save();
            }
        }
    }

    /**
     * get option variable of resource
     *
     * @return \Illuminate\Http\Response
     */

    public function getOptionVariables(Request $request)
    {
        $option_id = $request->option_id;
        $option_value = $request->option_value;

        $selected_variables = [];
        if (!empty($request->package_id)) {
            $selected_variables = json_decode(Package::where('id', $request->package_id)->first()->option_variables);
        }

        $option_variables = PackageVariable::whereIn('id', $selected_variables)->where('variable_options', $option_id)->where('option_value', $option_value)->first();

        return $option_variables;
    }
}
