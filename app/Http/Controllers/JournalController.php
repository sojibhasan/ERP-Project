<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Http\Requests\JournalRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Journal;
use App\System;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Superadmin\Entities\ModulePermissionLocation;

class JournalController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (request()->ajax()) {

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $journal = Journal::leftjoin('users', 'journals.added_by', 'users.id')
                ->leftjoin('accounts', 'journals.account_id', 'accounts.id')
                ->where('journals.business_id', $business_id)
                ->select(
                    'journals.*',
                    'accounts.name as account_name',
                    'users.username as user'
                );


            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $journal->whereDate('date', '>=', $start)
                    ->whereDate('date', '<=', $end);
            }
            if (!empty(request()->account_id)) {
                $journal->where('journals.account_id', request()->account_id);
            }
            if (!empty(request()->location_id)) {
                $journal->where('journals.location_id', request()->location_id);
            } else {
                $allowed_locations = ModulePermissionLocation::getModulePermissionLocations($business_id, 'accounting_module');
                if (!empty($allowed_locations)) {
                    if (!empty($allowed_locations->locations)) {
                        $location_ids = array_keys($allowed_locations->locations);
                        $journal->whereIn('journals.location_id',  $location_ids);
                    }
                }
            }

            $journal->groupBy('id');
            if ($account_access == 0) {
                $journal = collect([]);
            }
            return Datatables::of($journal)
                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="' . action('JournalController@edit', [$row->id]) . '" class="journal_edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                    
                    <li><a data-href="' . action('JournalController@destroy', [$row->journal_id]) . '" class="delete_journal"><i class="glyphicon glyphicon-trash" style="color:brown; cursor: pointer;"></i> Delete</a></li>
                    ';

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('debit_amount', '@if(!empty($debit_amount)){{@num_format($debit_amount)}}@endif')
                ->editColumn('credit_amount', '@if(!empty($credit_amount)){{@num_format($credit_amount)}}@endif')
                ->editColumn('date', '{{\Carbon::parse($date)->format("Y-m-d")}}')
                ->editColumn('account_id', function ($row) {
                    $debit_account_id = Account::where('id', $row->debit_account_id)->first();
                    if ($debit_account_id) {
                        return $debit_account_id->name;
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $accounts = Account::where('business_id', $business_id)->notClosed()->whereNull('default_account_id')->pluck('name', 'id');

        return view('journals.index')->with(compact('business_locations', 'account_access', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $accounts = Account::where('business_id', $business_id)->notClosed()->whereNull('default_account_id')->pluck('name', 'id');
        $locations = BusinessLocation::forDropdown($business_id);
        $default_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;
        $account_types = AccountType::forDropdown($business_id, false, false);
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $journal_last = Journal::where('business_id', $business_id)->select('journal_id')->distinct('journal_id')->count();
        $journal_id = !empty($journal_last) ? $journal_last + 1 : 1;


        return view('journals.create')->with(compact('accounts', 'locations', 'account_access', 'default_location_id', 'journal_id', 'account_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        if (!$account_access) {
            $output =  [
                'success' => 0,
                'msg' => System::getProperty('not_enalbed_module_user_message')
            ];
            return redirect()->back()->with('status', $output);
        }

        $validator = Validator::make($request->all(), [
            'location_id' => 'required',
            'date' => 'required',
            'debit_total' => 'required',
            'credit_total' => 'required|same:debit_total'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {

            $journal_id = !empty($request->journal_id) ? $request->journal_id : 1;
            $journals = $request->journal;

            foreach ($journals as $journal) {
                $note = !empty($request->note) ? $request->note : null;
                $data = array(
                    'business_id' => $business_id,
                    'journal_id' => $journal_id,
                    'location_id' => $request->location_id,
                    'date' => $this->transactionUtil->uf_date($request->date),
                    'debit_amount' => !empty($journal['debit_amount']) ? $journal['debit_amount'] : null,
                    'credit_amount' => !empty($journal['credit_amount']) ? $journal['credit_amount'] : null,
                    'account_type_id' => $journal['account_type_id'],
                    'account_id' => $journal['account_id'],
                    'note' => $note,
                    'is_opening_balance' => $request->is_opening_balance,
                    'added_by' => Auth::user()->id
                );
                $journal = Journal::create($data);
                if (!empty($journal->debit_amount)) {
                    $type = 'debit';
                    $amount = $journal->debit_amount;
                } else {
                    $type = 'credit';
                    $amount = $journal->credit_amount;
                }
                $acc_tran = array(
                    'account_id' => $journal->account_id,
                    'type' => $type,
                    'amount' => $amount,
                    'operation_date' => $this->transactionUtil->uf_date($request->date),
                    'created_by' => $journal->added_by,
                    'note' => $note,
                    'journal_entry' => $journal->id,
                );

                AccountTransaction::create($acc_tran);
                if ($request->is_opening_balance == 'yes') {
                    app('App\Http\Controllers\AccountController')->addAccountOpeningBalance($amount, $journal['account_id'], $this->transactionUtil->uf_date($request->date), $note);
                }
            }


            $output = [
                'success' => 1,
                'msg' => __('account.journal_add_succuss')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
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
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);
        $accounts = Account::where('business_id', $business_id)->notClosed()->pluck('name', 'id');
        $account_types = AccountType::forDropdown($business_id, false, false);
        $journal =   Journal::find($id);
        $journal_id = $journal->journal_id;
        $journals = Journal::where('business_id', $business_id)->where('journal_id', $journal_id)->get();

        return view('journals.edit')->with(compact('journals', 'journal', 'accounts', 'locations', 'account_types'));
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
        try {
            $business_id = request()->session()->get('business.id');
            $journals = $request->journal;

            DB::beginTransaction();
            foreach ($journals as $journal) {
                $note = !empty($request->note) ? $request->note : null;
                $data = array(
                    'business_id' => $business_id,
                    'journal_id' => !empty($request->journal_id) ? $request->journal_id : null,
                    'location_id' => !empty($request->location_id) ? $request->location_id : null,
                    'date' => $this->transactionUtil->uf_date($request->date),
                    'debit_amount' => !empty($journal['debit_amount']) ? $journal['debit_amount'] : null,
                    'credit_amount' => !empty($journal['credit_amount']) ? $journal['credit_amount'] : null,
                    'account_type_id' => $journal['account_type_id'],
                    'account_id' => $journal['account_id'],
                    'note' =>  $note,
                    'added_by' => Auth::user()->id
                );
                if (!empty($journal['id'])) {
                    Journal::where('id', $journal['id'])->update($data);
                } else {
                    $new_journal = Journal::create($data);
                }

                if (!empty($journal['debit_amount'])) {
                    $type = 'debit';
                    $amount = $journal['debit_amount'];
                } else {
                    $type = 'credit';
                    $amount = $journal['credit_amount'];
                }
                $acc_tran = array(
                    'account_id' => $journal['account_id'],
                    'type' => $type,
                    'amount' => $amount,
                    'operation_date' => $this->transactionUtil->uf_date($request->date),
                    'created_by' => Auth::user()->id,
                    'note' =>  $note,
                );
                if (!empty($journal['id'])) {
                    AccountTransaction::where('journal_entry', $journal['id'])->update($acc_tran);
                } else {
                    $acc_tran['journal_entry'] = $new_journal->id;
                    AccountTransaction::create($acc_tran);
                }

                if ($request->is_opening_balance == 'yes') {
                    $openign_balance = AccountTransaction::where('sub_type', 'opening_balance')->where('account_id', $journal['account_id'])->first();
                    if(!empty($openign_balance)){
                        $openign_balance->note =  $note;
                        $openign_balance->amount =  $amount;
                        $openign_balance->operation_date =  $this->transactionUtil->uf_date($request->date);
                        $openign_balance->save();

                    }else{
                        app('App\Http\Controllers\AccountController')->addAccountOpeningBalance($amount, $journal['account_id'], $this->transactionUtil->uf_date($request->date), $note);
                    }
                }
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('account.journal_update_succuss')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $journals = Journal::where('journal_id', $id)->get();
            foreach ($journals as $journal) {
                $account_transaction = AccountTransaction::where('journal_entry', $journal->id)->first();

                if ($account_transaction->type == 'debit') {
                    $type = 'credit';
                }
                if ($account_transaction->type == 'credit') {
                    $type = 'debit';
                }

                AccountTransaction::where('id', $account_transaction->id)->update([
                    'type' => $type,
                    'journal_deleted' => 1
                ]);
            }
            // $journals->delete();
            $output = [
                'success' => 1,
                'msg' => __('account.journal_delete_succuss')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }

    /**
     * Get row for journals enteries
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function getRow(Request $request)
    {
        $index = $request->index;
        $business_id = request()->session()->get('business.id');
        $accounts = Account::where('business_id', $business_id)->notClosed()->whereNull('default_account_id')->pluck('name', 'id');
        $account_types = AccountType::forDropdown($business_id, false, false);

        return view('journals.get_row')->with(compact('accounts', 'index', 'account_types'));
    }
    public function getAccountDropdownByAccountType($account_type_id)
    {
        $accounts = Account::getAccountByAccountTypeId($account_type_id);

        return $this->transactionUtil->createDropdownHtml($accounts, 'Please select');
    }
}
