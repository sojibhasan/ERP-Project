<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\PumpOperator;

class RecoverShortageController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('petro::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $pump_operator_id = request()->pump_operator_id;
            $pump_operator_details = PumpOperator::leftjoin('business', 'pump_operators.business_id', 'business.id')
                ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select('pump_operators.*', 'business_locations.name as location_name')
                ->first();

            $total_shortage = $this->transactionUtil->getPumpOperatorExcessOrShortage($pump_operator_id, 'shortage');

            $payment_types = $this->transactionUtil->payment_types();
            $clati = 0;
            $current_assets_account_type_ids = AccountType::where('name', 'Current Assets')->where('business_id', $business_id)->first();
            if (!empty($current_assets_account_type_ids)) {
                $clati = $current_assets_account_type_ids->id;
            }
            //Accounts
            $shortage_recover_account_id = Account::where('business_id', $business_id)->where('name', 'Shortage Receivable Account')->where('is_closed', 0)->first();
            $accounts = Account::where('business_id', $business_id)->where('account_type_id', $clati)->where('is_closed', 0)->where('is_main_account', 0)->orderBy('name', 'asc')->pluck('name', 'id');

            $prefix_type = 'shortage_recover';

            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
            //Generate reference number
            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);


            return view('petro::recover_shortage.create')
                ->with(compact('total_shortage', 'pump_operator_details', 'payment_types', 'accounts', 'pump_operator_id', 'shortage_recover_account_id', 'payment_ref_no'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $pump_operator_id = $request->pump_operator_id;

            $business_id =  Auth::user()->business_id;
            $inputs = $request->only([
                'amount', 'method', 'account_id', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number', 'payment_ref_no', 'account_receivable_id'
            ]);

            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'));
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
            $inputs['created_by'] = auth()->user()->id;
            $inputs['payment_for'] = $pump_operator_id;
            $inputs['business_id'] = $request->session()->get('business.id');

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (!empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            //Upload documents if added
            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            $this->transactionUtil->payAtOnceExcessShortage($inputs, 'shortage', $pump_operator_id);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('purchase.payment_added_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('petro::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $transaction id
     * @return Renderable
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $transaction_payment = TransactionPayment::find($id);
            $transaction = Transaction::find($transaction_payment->transaction_id);
            $pump_operator_id = $transaction->pump_operator_id;
            $pump_operator_details = PumpOperator::leftjoin('business', 'pump_operators.business_id', 'business.id')
                ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select('pump_operators.*', 'business_locations.name as location_name')
                ->first();

            $total_shortage = $this->transactionUtil->getPumpOperatorExcessOrShortage($pump_operator_id, 'shortage');

            $payment_types = $this->transactionUtil->payment_types();
            $clati = 0;
            $current_assets_account_type_ids = AccountType::where('name', 'Current Assets')->where('business_id', $business_id)->first();
            if (!empty($current_assets_account_type_ids)) {
                $clati = $current_assets_account_type_ids->id;
            }
            //Accounts
            $shortage_recover_account_id = Account::where('business_id', $business_id)->where('name', 'Shortage Receivable Account')->where('is_closed', 0)->first();
            $accounts = Account::where('business_id', $business_id)->where('account_type_id', $clati)->where('is_closed', 0)->where('is_main_account', 0)->orderBy('name', 'asc')->pluck('name', 'id');


            return view('petro::recover_shortage.edit')
                ->with(compact('total_shortage', 'pump_operator_details', 'payment_types', 'accounts', 'pump_operator_id', 'shortage_recover_account_id', 'transaction', 'transaction_payment'));
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
            $pump_operator_id = $request->pump_operator_id;
            $transaction_payment_id = $request->transaction_payment_id;
            $transaction_payment = TransactionPayment::find($transaction_payment_id);
            $pump_operator = PumpOperator::find($pump_operator_id);

            $business_id =  Auth::user()->business_id;
            $inputs = $request->only([
                'amount', 'method', 'account_id', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number', 'payment_ref_no', 'account_receivable_id'
            ]);

            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'));
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
            $inputs['created_by'] = auth()->user()->id;
            $inputs['payment_for'] = $pump_operator_id;
            $inputs['business_id'] = $request->session()->get('business.id');

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (!empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            //Upload documents if added
            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();
            //delete previous transaction and create new
            AccountTransaction::where('transaction_payment_id', $transaction_payment_id)->forcedelete();
            $pump_operator->short_amount = $pump_operator->short_amount + $transaction_payment->amount;
            $pump_operator->save();

            $transaction_payment->delete();
            $this->transactionUtil->updatePaymentStatus($transaction_payment->transaction_id);

            $this->transactionUtil->payAtOnceExcessShortage($inputs, 'shortage', $pump_operator_id);





            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('purchase.payment_added_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $transaction_payment = TransactionPayment::find($id);
            $transaction = Transaction::find($transaction_payment->transaction_id);
            $pump_operator_id = $transaction->pump_operator_id;
            $pump_operator = PumpOperator::find($pump_operator_id);

            DB::beginTransaction();
            AccountTransaction::where('transaction_payment_id', $id)->forcedelete();
            $pump_operator->short_amount = $pump_operator->short_amount + $transaction_payment->amount;
            $pump_operator->save();

            $transaction_payment->delete();
            DB::commit();
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
        return $output;
    }
}
