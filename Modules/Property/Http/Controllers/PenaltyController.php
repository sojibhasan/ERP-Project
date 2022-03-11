<?php

namespace Modules\Property\Http\Controllers;

use App\AccountTransaction;
use App\ContactLedger;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\Penalty;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyAccountSetting;
use Modules\Property\Entities\PropertySellLine;

class PenaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('property::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $sell_line_id = request()->sell_line_id;



        return view('property::penalty.create')->with(compact(
            'sell_line_id'
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
        try {
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['date'] = !empty($input['date']) ? Carbon::createFromFormat(session('business.date_format'), $input['date'])->toDateString() : date('Y-m-d');

            $sell_line = PropertySellLine::find($request->sell_line_id);
            $input['transaction_id'] = $sell_line->transaction_id;
            $input['block_id'] = $sell_line->block_id;
            $input['created_by'] = Auth::user()->id;

            Penalty::create($input);

            $transaction = Transaction::find($sell_line->transaction_id);
            $account_settings = PropertyAccountSetting::where('property_id',  $sell_line->property_id)->first();
            //capital amount transactions
            $account_transaction_data = [
                'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
                'amount' => $input['amount'],
                'account_id' => $account_settings->account_receivable_account_id,
                'type' => 'debit',
                'operation_date' =>  !empty($inputs['date']) ? $inputs['date'] : date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
                'transaction_id' => !empty($transaction) ? $transaction->id : null,
                'transaction_sell_line_id' =>  !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,
                'income_type' => 'penalty income'
            ];

            //penalty amount transactions
            $account_transaction_data['income_type'] = 'penalty income';
            $account_transaction_data['account_id'] = $account_settings->account_receivable_account_id;
            $account_transaction_data['type'] = 'debit';
            AccountTransaction::createAccountTransaction($account_transaction_data);
            ContactLedger::createContactLedger($account_transaction_data);

            $account_transaction_data['account_id'] = $account_settings->penalty_income_account_id;
            $account_transaction_data['type'] = 'credit';
            AccountTransaction::createAccountTransaction($account_transaction_data);

            $output = [
                'success' => true,
                'msg' => __('property::lang.penalty_added_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $penalties = Penalty::leftjoin('users', 'penalties.created_by', 'users.id')
            ->where('sell_line_id', $id)->select('penalties.*', DB::raw("CONCAT(users.first_name,' ', users.last_name) as username"))->get();
        $show_delete = request()->show_delete;


        return view('property::penalty.show')->with(compact(
            'penalties',
            'show_delete'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
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
}
