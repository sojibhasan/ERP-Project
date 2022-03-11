<?php

namespace App\Http\Controllers\Chequer;

use App\Account;
use App\Chequer\ChequeNumberMaintain;
use App\Chequer\ChequerBankAccount;
use App\Chequer\ChequerCurrency;
use App\Chequer\ChequerDefaultSetting;
use App\Chequer\ChequerPurchaseOrder;
use App\Chequer\ChequerStamp;
use App\Chequer\ChequerSupplier;
use App\Chequer\ChequeTemplate;
use App\Chequer\PrintedChequeDetail;
use App\Contact;
use App\Events\TransactionPaymentAdded;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\TransactionPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;

class ChequeWriteController extends Controller
{

    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $getvoucher = PrintedChequeDetail::where('business_id', $business_id)->orderBy('id', 'desc')->get();
        $templates = ChequeTemplate::getTemplates($business_id);
        $results = Contact::where('business_id', $business_id)->where('type', 'supplier')->get();
        $stamps = ChequerStamp::where('business_id', $business_id)->where('stamp_status', 1)->get();
        $get_defultvalu = ChequerDefaultSetting::where('business_id', $business_id)->get();
        $get_currency = ChequerCurrency::get();
        $get_bankacount = Account::where('business_id', $business_id)->get();

        return view('chequer/write_cheque/create')->with(compact(
            'getvoucher',
            'templates',
            'results',
            'stamps',
            'get_defultvalu',
            'get_currency',
            'get_bankacount'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $temp_id = null;
        $data['template_id'] = $request->template_id;
        $data['user_id'] = Auth::user()->id;
        $data['payee'] = $request->payee;
        $data['cheque_no'] = $request->cheque_no;
        $data['cheque_date'] = $request->cheque_date;
        $data['cheque_amount'] = $request->cheque_amount;
        $data['supplier_paid_amount'] = $request->paid_to_supplier;
        $data['purchase_order_id'] = $request->purchase_id;
        $payable_amount = $request->payable_amount;
     

        if ($request->purchase_id) {
            $purchase_data = ChequerPurchaseOrder::where('id', $request->purchase_id)->get();
            foreach ($purchase_data as $data) {
                $us_id      = Auth::user()->id;
                $tm       = date('Y-m-d H:i:s', time());
                $orderid    = $data->id;
                $supplier   = $data->supplier_name;
                $supplierid   = $data->supplier_id;
                $totalamount  = $payable_amount;
                $outletid   = $data->outlet_id;
                $outletname   = $data->outlet_name;
            }

            $value = array(
                'Bcheque' => $request->cheque_no,
                'Bcheque_bank' => '',
                'Bcheque_date' => $request->cheque_date,
                'addi_card_numb' => '',
                'card_numb' => '',
                'cheque' => '',
                'cheque_account_C' => '',
                'cheque_bank' => '',
                'cheque_date' => '',
                'customer' => '',
                'paid' => $request->paid_to_supplier,
                'paid_by' => Auth::user()->id
            );

            if (!empty($value)) {
                $cheque_bank = '';
                $totalpaudamount = 0;
                $payment = $value;
                // $totalpaudamount  = $totalpaudamount + $value['paid'];
                $paid_amt     = $value['paid'];
                $cheque       = $value['cheque'];
                $cheque_date = date('Y-m-d');
                $cheque_bank = $value['cheque_bank'];
                $addi_card_numb   = $value['addi_card_numb'];
                $giftcard_numb    = $value['card_numb'];
                $cheque_value   = $value['cheque_bank'];
                $payment_method_id  = $value['paid_by'];
                $cheque_bank = $value['Bcheque_bank'];

                if ($value['paid_by'] != 12) {
                    // $getPayMethodData = $this->Constant_model->getDataOneColumn('payment_method', 'id', $payment_method_id);
                    // if (count($getPayMethodData) == 1) {
                    //     $payMethod_name   = $getPayMethodData[0]->name;
                    //     $payMethod_balance  = $getPayMethodData[0]->balance;
                    // }
                } else {
                    $payMethod_name = "Cheque";
                    $cheque       = $value['Bcheque'];
                    $cheque_date = date('Y-m-d');
                    $cheque_bank = $value['Bcheque_bank'];
                    $cheque_value   = $value['Bcheque_bank'];
                }

                // $ins_order_data = array(
                //     'purchase_id'   => $orderid,
                //     'grandtotal'    => $totalamount,
                //     'supplier_id'   => $supplierid,
                //     'supplier_name'   => $supplier,
                //     'gift_card'     => $giftcard_numb,
                //     'payment_method'  => $payment_method_id,
                //     'payment_name'    => $payMethod_name,
                //     'cheque_number'   =>$request->cheque_no,
                //     'cheque_date'   => $cheque_date,
                //     'cheque_bank'   => $cheque_bank,
                //     'paid_amt'      => $paid_amt,
                //     'paid_date'     => $tm,
                //     'outlet_id'     => $outletid,
                //     'outlet_name'   => $outletname,
                //     'created_by'    => $us_id,
                //     "card_number"   => $addi_card_numb,
                //     'bank_number'    => $cheque_bank,
                //     'transaction_date' => date("Y-m-d")
                // );
                // $this->db->insert('purchase_bills', $ins_order_data);


                //creating payment transaction
                $business_id = $request->session()->get('user.business_id');
                $transaction_id = $request->input('purchase_id');
                $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);

                if ($transaction->payment_status != 'paid') {
                    $inputs = [
                        'amount' => $request->cheque_amount,
                        'method' => 'cheque',
                        'note' => null,
                        'card_number' => null,
                        'card_holder_name' => null,
                        'card_transaction_number' => null,
                        'card_type' => null,
                        'card_month' => null,
                        'card_year' => null,
                        'card_security' => null,
                        'cheque_number' => $request->cheque_no,
                        'bank_account_number' => null
                    ];
                    $inputs['paid_on'] = $this->transactionUtil->uf_date(date("d/m/Y H:i"), true);
                    $inputs['transaction_id'] = $transaction->id;
                    $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                    $inputs['created_by'] = auth()->user()->id;
                    $inputs['payment_for'] = $transaction->contact_id; // we have ignored current system contact/suppplier

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

                    $prefix_type = 'purchase_payment';
                    if (in_array($transaction->type, ['sell', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    } elseif ($transaction->type == 'expense') {
                        $prefix_type = 'expense_payment';
                    }

                    DB::beginTransaction();
                    PrintedChequeDetail::create($data);

                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                    //Generate reference number
                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                    $inputs['business_id'] = $request->session()->get('business.id');
                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                    $tp = TransactionPayment::create($inputs);

                    //update payment status
                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                    $inputs['transaction_type'] = $transaction->type;
                    event(new TransactionPaymentAdded($tp, $inputs));
                    DB::commit();
                }




















                // $purchase_bill_last_id = $this->db->insert_id();

                // if (!empty($giftcard_numb)) {
                //     $ckGiftResult = $this->db->query("SELECT * FROM gift_card WHERE card_number = '$giftcard_numb' ");
                //     $ckGiftRows = $ckGiftResult->num_rows();
                //     if ($ckGiftRows == 1) {
                //         $ckGiftData = $ckGiftResult->result();
                //         $ckGift_id = $ckGiftData[0]->id;
                //         $upd_gift_data = array(
                //             'status' => '1',
                //             'updated_user_id' => $us_id,
                //             'updated_datetime' => $tm,
                //         );
                //         $this->Constant_model->updateData('gift_card', $upd_gift_data, $ckGift_id);
                //     }
                // }

                // if ($value['paid_by'] != 12) {
                //     $pay_query = $this->db->get_where('payment_method', array('id' => $payment_method_id))->row();
                //     $pay_balance = $pay_query->balance;
                //     $now_balance = $pay_balance - $paid_amt;

                //     $pay_data  = array(
                //         'balance'     => $now_balance,
                //         'updated_user_id' => $us_id,
                //         'updated_datetime'  => $tm,
                //     );
                //     $this->db->update('payment_method', $pay_data, array('id' => $payment_method_id));

                //     $trans_ins = array(
                //         'order_id'      => $orderid,
                //         'account_number'  => $payment_method_id,
                //         'bring_forword '  => $pay_balance,
                //         'outlet_id'     => $outletid,
                //         'trans_type'    => 'payment_s',
                //         'amount'      => $value['paid'],
                //         'cheque_number'   => $this->input->post('cheque_no'),
                //         'card_number'   => $addi_card_numb,
                //         'cheque_date'   => date('Y-m-d'),
                //         'created_by'    => $us_id,
                //         'created'     => date('Y-m-d H:i:s'),
                //         'transaction_type' => 'supplier_payment_from_pm',
                //         'purchase_bill_last_id' => $purchase_bill_last_id,
                //         'transaction_date' => date('Y-m-d')
                //     );
                //     $this->db->insert('transactions', $trans_ins);
                // } else {
                //     $bank_bal = $this->db->get_where('bank_accounts', array('id' => $value['Bcheque_bank']))->row();
                //     $bank_val = $bank_bal->current_balance;
                //     $totalamt = $bank_val - $value['paid'];
                //     $paybal   = array('current_balance' => $totalamt);
                //     $this->Constant_model->updateData('bank_accounts', $paybal, $cheque_bank);

                //     $trans_ins = array(
                //         'order_id'      => $orderid,
                //         'account_number'  => $value['Bcheque_bank'],
                //         'bring_forword '  => $bank_val,
                //         'outlet_id'     => $outletid,
                //         'trans_type'    => 'payment_s',
                //         'amount'      => $value['paid'],
                //         'cheque_number'   => $cheque,
                //         'card_number'   => $addi_card_numb,
                //         'cheque_date'   => date('Y-m-d'),
                //         'created_by'    => $us_id,
                //         'transaction_type' => 'supplier_payment_from_bank',
                //         'created'     => date('Y-m-d H:i:s'),
                //         'transfer_status' => 1,
                //         'transaction_date' => date("Y-m-d")
                //     );
                //     $this->db->insert('transactions', $trans_ins);

                $cheque_no_for_update = $cheque;
                $cheque_status = array(
                    'status' => 1
                );
                // $this->Constant_model->updateChequeStatus($cheque_status, $cheque_no_for_update);
                ChequeNumberMaintain::where('cheque_no', $cheque_no_for_update)->update($cheque_status);
                // }
            }

            // $purchaseorderdetails = $this->db->select('paid_amt')->where('id', $orderid)->get('purchase_order')->row_array();

            // $ins_order_data = array(
            //     'paid_amt' => $totalpaudamount,
            //     'updated_user_id' => $this->session->userdata('user_id'),
            //     'payment_method_name' => $payMethod_name,
            //     'updated_datetime' => $tm,
            // );
            // $this->Constant_model->updateData('purchase_order', $ins_order_data, $orderid);
        }

        // $this->session->set_flashdata('alert_msg', array('success', 'Add category status', "Template added successfully"));

        $response = array(
            'status' => 1,
            // 'url' => base_url() . 'printed_cheque_details'
        );
        // $response['payment_collection'] = $payment_collection;
        echo json_encode($response);
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

    public function getChequeNoUniqueOrNotCheck(Request $request)
    {
        $cheque_no = $request->chequeNo;
        $payee = $request->supplierid;
        $business_id = $request->session()->get('business.id');

        $json['cheque_check'] = PrintedChequeDetail::where('cheque_no', $cheque_no)->where('payee', $payee)->where('business_id', $business_id)->first();

        return json_encode($json);
    }

    public function getTempleteWiseBankAccounts(Request $request)
    {
        $templtID = $request->templtID;
        $getBankacounts = ChequerBankAccount::where('is_visible', 1)->where('cheque_templete_id', $templtID)->get();
        $banks = '<option value="">None</option>';
        if (!empty($getBankacounts)) {
            foreach ($getBankacounts as $getbankacount) {
                $banks .= "<option value='" . $getbankacount['account_number'] . "' >" . $getbankacount['account_number'] . "</option>";
            }
        }
        $json['banks']    = $banks;

        echo json_encode($json);
    }

    public function listOfPayeeTemp(Request $request)
    {
        $supplier_id = $request->supplier_id;
        // $json['results']=$this->Common_model->get_data_by_query("select * from printed_cheque_details where payee='$myText' group by payee_tempname");
        // $json['get_purchase_order'] =$this->Common_model->get_data_by_query("select id from purchase_order where supplier_id='$myText'");

        $json['results'] = PrintedChequeDetail::where('payee', $supplier_id)->groupBy('payee_tempname')->get();
        $json['get_purchase_order'] = Transaction::where('contact_id', $supplier_id)->where('type', 'purchase')->where('payment_status', '!=', 'paid')->get();

        echo json_encode($json);
    }

    public function getPurchaseOrderDataById(Request $request)
    {
        $purchase_id = $request->purchase_id;
        // $json['results'] =$this->Common_model->get_data_by_query("select * from purchase_order where id='$purchase_id'");
        // $json['results'] = ChequerPurchaseOrder::where('id', $purchase_id)->first(); 

        $amount = 0;
        $grandtotal = 0;
        // $purchase_bill = $this->db->query("SELECT purchase_id, SUM(paid_amt) as amount FROM purchase_bills WHERE purchase_id= ".$purchase_id)->result();
        // $purchase_data = $this->db->query("select * from purchase_order where id='$purchase_id'")->result();
        $purchase_data = Transaction::join('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->where('transactions.id', $purchase_id)->first();
        // foreach ($purchase_bill as $value) {
        // 	$amount = $value->amount;
        // }
        $json['purchase_bill_no'] =  $purchase_data->ref_no;
        // foreach ($purchase_data as $data) {
        //     $grandtotal = $data->final_total;
        // }
        $unpaid_amt = 0;
        $unpaid_amt = $purchase_data->final_total - $purchase_data->amount; // $amount - $grandtotal;
        $json['dueamount'] = $unpaid_amt;

        echo json_encode($json);
    }

    public function checkTemplateId(Request $request)
    {
        $printchaque_id = $request->printchaque_id;
        // $data['get_cheaquetemp']=$this->Common_model->get_data_by_query("select * from printed_cheque_details as pcd left join cheque_templates as ct on pcd.template_id=ct.id where pcd.id='$printchaque_id' ");
        $data['get_cheaquetemp'] = PrintedChequeDetail::join('cheque_templates', 'printed_cheque_details.template_id', 'cheque_templates.id')->where('printed_cheque_details.id', $printchaque_id)->get();
        foreach ($data['get_cheaquetemp'] as $chaquetempid) {
        }
        $stampvalu = $chaquetempid['stampvalu'];
        // $stamdata=$this->Common_model->get_data_by_query("select * from stamps_table where stamp_status='1' and stamp_id='$stampvalu'");
        $stamdata = ChequerStamp::where('id', $stampvalu)->where('stamp_status', 1)->get();
        foreach ($stamdata as $stamdetlials) {
        }
        echo $chaquetempid['template_id'] . ',' . $chaquetempid['id'], ',' . $chaquetempid['stampvalu'] . ',' . $chaquetempid['stamp_image'] . ',' . $chaquetempid['cheque_amount'] . ',' . $chaquetempid['is_strikeBearer'] . ',' . $chaquetempid['is_dublecross'] . ',' . $chaquetempid['amount_word'];
    }

    public function getTemplatechaque(Request $request)
    {
        $id = $request->id;
        // $values = $this->db->get_where('cheque_templates', array('id' => $id))->row();
        $values = ChequeTemplate::where('id', $id)->first();
        echo json_encode($values);
    }

    public function getNextChequedNO(Request $request)
    {
        // $bankData = $this->db->query("select id from bank_accounts where account_number LIKE '". $this->input->post('bankacount') ."'")->row();
        $business_id = request()->session()->get('business.id');
        $bankData = Account::where('business_id', $business_id)->where('id', $request->bankacount)->first();
        if (!empty($bankData)) {
            // $this->db->where('account_no', $bankData->id);
            // $this->db->where('status', 0);
            // $this->db->limit(1);
            // $query = $this->db->get('cheque_number_maintain');
            $query = ChequeNumberMaintain::where('account_no', $bankData->id)->where('status', 0)->first();
            if ($query->num_rows() > 0) {
                echo $query->cheque_no;
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }
}
