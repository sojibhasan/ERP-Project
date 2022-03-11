<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\Currency;
use App\System;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Superadmin\Entities\PayOnline;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;
use Yajra\DataTables\Facades\DataTables;

class PayOnlineController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $pay_onlines = PayOnline::join('business', 'pay_onlines.business_id', '=', 'business.id')
                ->select('pay_onlines.*');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $pay_onlines->whereDate('date', '>=', request()->start_date);
                $pay_onlines->whereDate('date', '<=', request()->end_date);
            }
            if (!empty(request()->name)) {
                $pay_onlines->where('pay_onlines.id', request()->name);
            }
            if (!empty(request()->pay_online_no)) {
                $pay_onlines->where('pay_onlines.id', request()->pay_online_no);
            }
            if (!empty(request()->currency)) {
                $pay_onlines->where('currency', request()->currency);
            }
            if (!empty(request()->status)) {
                $pay_onlines->where('status', request()->status);
            }
            if (!empty(request()->paid_via)) {
                $pay_onlines->where('paid_via', request()->paid_via);
            }
            return DataTables::of($pay_onlines)
                ->addColumn(
                    'action',
                    '@if($paid_via == "offline")<button data-href ="{{action(\'\Modules\Superadmin\Http\Controllers\PayOnlineController@edit\',[$id])}}" class="btn btn-info btn-xs change_status" data-toggle="modal" data-target="#statusModal">
                            @lang( "superadmin::lang.status")
                            </button>@endif'
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('paid_via', '{{ucfirst($paid_via)}}')
                ->editColumn(
                    'status',
                    '@if($status == "approved")
                                <span class="label bg-light-green">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @elseif($status == "pending")
                                <span class="label bg-aqua">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @else($status == "declined")
                                <span class="label bg-red">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @endif'
                )
                ->editColumn(
                    'amount',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$amount}}">{{$amount}}</span>'
                )
                ->addColumn(
                    'name',
                    '{{$first_name}} {{$last_name}}'
                )
                ->removeColumn('id')
                ->rawColumns(['action', 'status', 'amount'])
                ->make(true);
        }

        $PAY_ONLINE_CURRENCY_TYPE = json_decode(System::getProperty('PAY_ONLINE_CURRENCY_TYPE'), true);
        $status = PayOnline::payment_status();
        $pay_online_nos = PayOnline::pluck('pay_online_no', 'id');
        $names = PayOnline::select('id', DB::raw("concat(first_name, ' ',  last_name) as name"))->pluck('name', 'id');
        $currencies = Currency::whereIn('id', $PAY_ONLINE_CURRENCY_TYPE)->select('code', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country')
            ->pluck('info', 'code');
        return view('superadmin::pay_online.index')->with(compact(
            'status',
            'names',
            'pay_online_nos',
            'currencies'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $order_id = Str::random(5);
        $PAY_ONLINE_CURRENCY_TYPE = json_decode(System::getProperty('PAY_ONLINE_CURRENCY_TYPE'), true);

        $currencies = Currency::whereIn('id', $PAY_ONLINE_CURRENCY_TYPE)->select('code', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country')
            ->pluck('info', 'code');

        $pay_online_starting_no = (int) env('PAY_ONLINE_STARTING_NO') ?? 1;
        $pay_online_count =     PayOnline::count();
        $pay_online_no =  $pay_online_starting_no + $pay_online_count;

        $reference_no = $business->company_number;
        if(session()->get('business.is_patient')){
            $reference_no = Auth::user()->username;
        }

        return view('superadmin::pay_online.create')->with(compact(
            'order_id',
            'currencies',
            'pay_online_no',
            'reference_no'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $order_id =  $request->order_id;
        try {
            $pay_online_data = [
                'business_id' => $business_id,
                'order_id' => $order_id,
                'date' => date('Y-m-d'),
                'pay_online_no' => $request->pay_online_no,
                'type' => 'security_deposit',
                'amount' => $request->amount,
                'reference_no' => $request->reference_no,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'currency' => $request->currency,
                'note' => $request->note,
                'paid_via' => 'offline',
                'payment_transaction_id' => null,
                'status' => 'pending'
            ];

            $pay_online = PayOnline::where('order_id', $order_id)->where('business_id', $business_id)->first();
            if (!empty($pay_online)) {
                PayOnline::where('id', $pay_online->id)->update($pay_online_data);
            } else {
                PayOnline::create($pay_online_data);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        $amount = $request->amount;
        return view('superadmin::pay_online.show_bank_details')->with(compact(
            'order_id',
            'amount'
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
            $status = PayOnline::payment_status();
            $pay_online = PayOnline::find($id);

            return view('superadmin::pay_online.edit')
                ->with(compact('pay_online', 'status'));
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
        try {
            $status = $request->status;
            $payment_transaction_id = $request->payment_transaction_id;

            PayOnline::where('id', $id)->update(['status' => $status, 'payment_transaction_id' => $payment_transaction_id]);

            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return  $output;
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
     * Payhere notify
     * @param int $id
     * @return Renderable
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

            $business_id = $payment_data->business_id;

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'completed';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);

            $pay_online = PayOnline::where('business_id', $business_id)->where('order_id', $order_id)->first();
            if (!empty($pay_online)) {
                $pay_online->status = 'approved';
                $pay_online->save();
            }

            $email = System::getProperty('email');

            //send payment added email notification to user
            Notification::route('mail', $email)
                ->notify(new NewSubscriptionNotification($pay_online));
        } else {
            $payment_data = DB::table('payhere')->where('order_id', $order_id)->first();
            $business_id = $payment_data->business_id;

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'declined';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);

            $pay_online = PayOnline::where('business_id', $business_id)->where('order_id', $order_id)->first();
            if (!empty($pay_online)) {
                $pay_online->status = 'declined';
                $pay_online->save();
            }
        }

        return true;
    }
    /**
     * Payhere notify
     * @param int $id
     * @return Renderable
     */
    public function initiatePayhere(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $order_id =  $request->order_id;
        $initial_data = array(
            'order_id' => $order_id,
            'business_id' => $business_id,
            'package_id' => null,
            'transaction_id' => null,
            'user_id' => auth()->user()->id,
            'price' => $request->amount,
            'currency' => $request->currency,
            'status' => 'pending'
        );
        $payment_data = DB::table('payhere')->where('order_id', $order_id)->where('business_id', $business_id)->first();
        if (!empty($payment_data)) {
            $payment_data = DB::table('payhere')->where('id', $payment_data->id)->update($initial_data);
        } else {
            $payment_data = DB::table('payhere')->insert($initial_data);
        }
        $payment_data = DB::table('payhere')->where('order_id', $order_id)->where('business_id', $business_id)->first();

        $pay_online_data = [
            'business_id' => $business_id,
            'order_id' => $order_id,
            'date' => date('Y-m-d'),
            'pay_online_no' => $request->pay_online_no,
            'type' => $request->type,
            'amount' => $request->amount,
            'reference_no' => $request->reference_no,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'currency' => $request->currency,
            'note' => $request->note,
            'paid_via' => 'payhere',
            'payment_transaction_id' => $payment_data->id,
            'status' => 'pending'
        ];

        $pay_online = PayOnline::where('order_id', $order_id)->where('business_id', $business_id)->first();
        if (!empty($pay_online)) {
            PayOnline::where('id', $pay_online->id)->update($pay_online_data);
        } else {
            PayOnline::create($pay_online_data);
        }

        $msg = __('lang_v1.success');

        $output = ['success' => 1, 'msg' => $msg];
        return $output;
    }
}
