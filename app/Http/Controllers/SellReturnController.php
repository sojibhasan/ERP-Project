<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\BusinessLocation;
use App\CashRegister;
use App\CashRegisterTransaction;
use App\ContactLedger;
use App\Transaction;
use App\TransactionSellLine;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ContactUtil $contactUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->join(
                    'transactions as T1',
                    'transactions.return_parent_id',
                    '=',
                    'T1.id'
                )
                ->leftJoin(
                    'transaction_payments AS TP',
                    'transactions.id',
                    '=',
                    'TP.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell_return')
                ->where('transactions.status', 'final')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.final_total',
                    'transactions.payment_status',
                    'bl.name as business_location',
                    'T1.invoice_no as parent_sale',
                    'T1.id as parent_sale_id',
                    DB::raw('SUM(TP.amount) as amount_paid')
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'SellReturnController@show\', [$parent_sale_id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                        <li><a href="{{action(\'SellReturnController@add\', [$parent_sale_id])}}" ><i class="fa fa-edit" aria-hidden="true"></i> @lang("messages.edit")</a></li>
                    @endif

                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="print-invoice" data-href="{{action(\'SellReturnController@printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>
                    @endif
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$this->productUtil->num_f($final_total)}}">{{$this->productUtil->num_f($final_total)}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return $row->is_pos_return . ' <button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="' . action('SellController@show', [$row->parent_sale_id]) . '">' . $row->parent_sale . '</button>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($due) . '">' . $this->productUtil->num_f($due) . '</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellReturnController@show', [$row->parent_sale_id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['final_total', 'action', 'parent_sale', 'payment_status', 'payment_due'])
                ->make(true);
        }

        return view('sell_return.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     if (!auth()->user()->can('sell.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');

    //     //Check if subscribed or not
    //     if (!$this->moduleUtil->isSubscribed($business_id)) {
    //         return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
    //     }

    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

    //     return view('sell_return.create')
    //         ->with(compact('business_locations'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $transaction = Transaction::where('business_id', $business_id)->where('invoice_no', $id)->first();

            if (empty($transaction)) {
                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong')
                ];
                return $output;
            } else {
                $id = $transaction->id;
            }
        }

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax', 'sell_lines.sub_unit', 'sell_lines.product', 'sell_lines.product.unit'])
            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }

            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity, false, null, true);
        }

        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('sale_return_data')->first();
        if (!empty($temp_data)) {
            $temp_data = json_decode($temp_data->sale_return_data);
        }
        if (!request()->session()->get('business.popup_load_save_data')) {
            $temp_data = [];
        }

        if (request()->ajax()) {
            return view('sell_return.add_ajax')
                ->with(compact('sell', 'temp_data'));
        }

        return view('sell_return.add')
            ->with(compact('sell', 'temp_data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['sale_return_data' => '']);

            $input = $request->except('_token');
            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');

                //Check if subscribed or not
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                }

                $user_id = $request->session()->get('user.id');

                $discount = [
                    'discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount']
                ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_id'], $discount);

                //Get parent sale
                $sell = Transaction::where('business_id', $business_id)
                    ->with(['sell_lines', 'sell_lines.sub_unit'])
                    ->findOrFail($input['transaction_id']);

                //Check if any sell return exists for the sale
                $sell_return = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell_return')
                    ->where('return_parent_id', $sell->id)
                    ->first();

                $sell_return_data = [
                    'transaction_date' => $this->productUtil->uf_date($request->input('transaction_date')),
                    'invoice_no' => $input['invoice_no'],
                    'discount_type' => $discount['discount_type'],
                    'discount_amount' => $this->productUtil->num_uf($input['discount_amount']),
                    'tax_id' => $input['tax_id'],
                    'tax_amount' => $invoice_total['tax'],
                    'total_before_tax' => $invoice_total['total_before_tax'],
                    'final_total' => $invoice_total['final_total']
                ];

                DB::beginTransaction();

                //Generate reference number
                if (empty($sell_return_data['invoice_no'])) {
                    //Update reference count
                    $ref_count = $this->productUtil->setAndGetReferenceCount('sell_return');
                    $sell_return_data['invoice_no'] = $this->productUtil->generateReferenceNumber('sell_return', $ref_count);
                }

                if (empty($sell_return)) {
                    $sell_return_data['business_id'] = $business_id;
                    $sell_return_data['location_id'] = $sell->location_id;
                    $sell_return_data['contact_id'] = $sell->contact_id;
                    $sell_return_data['customer_group_id'] = $sell->customer_group_id;
                    $sell_return_data['type'] = 'sell_return';
                    $sell_return_data['status'] = 'final';
                    $sell_return_data['created_by'] = $user_id;
                    $sell_return_data['return_parent_id'] = $sell->id;
                    $sell_return = Transaction::create($sell_return_data);
                } else {
                    $sell_return->update($sell_return_data);
                }

                if ($request->session()->get('business.enable_rp') == 1 && !empty($sell->rp_earned)) {
                    $is_reward_expired = $this->transactionUtil->isRewardExpired($sell->transaction_date, $business_id);
                    if (!$is_reward_expired) {
                        $diff = $sell->final_total - $sell_return->final_total;
                        $new_reward_point = $this->transactionUtil->calculateRewardPoints($business_id, $diff);
                        $this->transactionUtil->updateCustomerRewardPoints($sell->contact_id, $new_reward_point, $sell->rp_earned);

                        $sell->rp_earned = $new_reward_point;
                        $sell->save();
                    }
                }

                //Update payment status
                $this->transactionUtil->updatePaymentStatus($sell_return->id, $sell_return->final_total);

                //Update quantity returned in sell line
                $returns = [];
                $product_lines = $request->input('products');
                foreach ($product_lines as $product_line) {
                    $returns[$product_line['sell_line_id']] = $product_line['quantity'];
                }
                foreach ($sell->sell_lines as $sell_line) {
                    if (array_key_exists($sell_line->id, $returns)) {
                        $multiplier = 1;
                        if (!empty($sell_line->sub_unit)) {
                            $multiplier = $sell_line->sub_unit->base_unit_multiplier;
                        }

                        $quantity = $this->transactionUtil->num_uf($returns[$sell_line->id]) * $multiplier;

                        $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);
                        $quantity_formated = $this->transactionUtil->num_f($quantity);

                        $sell_line->quantity_returned = $quantity;
                        $sell_line->save();

                        //update quantity sold in corresponding purchase lines
                        $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, $quantity_formated, $quantity_before);

                        // Update quantity in variation location details
                        $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, $quantity_formated, $quantity_before);


                        $product = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                            ->where('transaction_sell_lines.id', $sell_line->id)
                            ->select('transaction_sell_lines.*', 'products.category_id', 'products.enable_stock', 'products.stock_type', 'products.sub_category_id', 'variations.default_purchase_price')
                            ->first();

                        $costofgood_account_id = $this->getProductCatAccountId($product, 'cogs');
                        $sale_income_account_id = $this->getProductCatAccountId($product, 'sale_income');

                        //create accounting transaction
                        $account_transaction_data = [
                            'amount' =>  $quantity * $product->default_purchase_price,
                            'account_id' => $costofgood_account_id,
                            'type' => 'credit',
                            'sub_type' => 'ledger_show',
                            'operation_date' => Carbon::now()->format('Y-m-d H:i:s'),
                            'created_by' => $sell_return->created_by,
                            'transaction_id' => $sell_return->id,
                            'note' => null
                        ];
                        //cost of goods sale transaction
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                        $account_transaction_data['contact_id'] = $sell_return->contact_id;
                        $account_transaction_data['amount'] = $quantity * $product->unit_price;
                        ContactLedger::createContactLedger($account_transaction_data);

                        //sale income aacount transaction
                        $account_transaction_data['amount'] =   $quantity * $product->unit_price;
                        $account_transaction_data['account_id'] =   $sale_income_account_id;
                        $account_transaction_data['sub_type'] =   null;
                        AccountTransaction::createAccountTransaction($account_transaction_data);

                        //stock account transaction
                        $account_transaction_data['account_id'] = $product->stock_type;
                        $account_transaction_data['amount'] =   $quantity * $product->default_purchase_price;
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                    }
                }

                $receipt = $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id);

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success'),
                    'receipt' => $receipt
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = [
                'success' => 0,
                'msg' => $msg
            ];
        }

        return $output;
    }

    public function getProductCatAccountId($product, $type)
    {
        //set defuatlt account id
        if ($type == 'cogs') {
            $account_id = $this->transactionUtil->account_exist_return_id('Cost of Goods Sold');
        } else {
            $account_id = $this->transactionUtil->account_exist_return_id('Sales Income');
        }
        // if sub cat is set
        if (!empty($product->sub_category_id)) {
            $account_id = $this->transactionUtil->getCategoryAccountId($product->sub_category_id, $type);
            //if sub cat accout is not set get cat aaccount
            if (empty($account_id)) {
                $account_id = $this->transactionUtil->getCategoryAccountId($product->category_id, $type);
            }
        } else {
            // if sub_cat is not set get by cat
            $account_id = $this->transactionUtil->getCategoryAccountId($product->category_id, $type);
        }

        return $account_id;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $sell = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(
                'contact',
                'return_parent',
                'tax',
                'sell_lines',
                'sell_lines.product',
                'sell_lines.variations',
                'sell_lines.sub_unit',
                'sell_lines.product',
                'sell_lines.product.unit',
                'location'
            )
            ->first();

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $sell_taxes = [];
        if (!empty($sell->return_parent->tax)) {
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }

        $total_discount = 0;
        if ($sell->return_parent->discount_type == 'fixed') {
            $total_discount = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'percentage') {
            $discount_percent = $sell->return_parent->discount_amount;
            if ($discount_percent == 100) {
                $total_discount = $sell->return_parent->total_before_tax;
            } else {
                $total_after_discount = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
                $total_before_discount = $total_after_discount * 100 / (100 - $discount_percent);
                $total_discount = $total_before_discount - $total_after_discount;
            }
        }

        $sell_return_child = Transaction::where('business_id', $business_id)->where('return_parent_id', $id)->first();

        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount', 'sell_return_child'));
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
     * Return the row for the product
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductRow()
    {
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => []
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);
            //If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                $output['html_content'] = view('sell_return.receipt', compact('receipt_details'))->render();
            }
        }
        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];

                $business_id = $request->session()->get('user.business_id');

                $transaction = Transaction::where('business_id', $business_id)
                    ->where('id', $transaction_id)
                    ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, 'browser');

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }


    /**
     * save pos page sale return and return total amount
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function savePosReturn(Request $request)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['sale_return_data' => '']);

            $input = $request->except('_token');

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');

                //Check if subscribed or not
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                }

                $user_id = $request->session()->get('user.id');

                $discount = [
                    'discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount']
                ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_id'], $discount);

                //Get parent sale
                $sell = Transaction::where('business_id', $business_id)
                    ->with(['sell_lines', 'sell_lines.sub_unit'])
                    ->findOrFail($input['transaction_id']);

                //Check if any sell return exists for the sale
                $sell_return = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell_return')
                    ->where('return_parent_id', $sell->id)
                    ->first();

                $sell_return_data = [
                    'transaction_date' => $this->productUtil->uf_date($request->input('transaction_date')),
                    'invoice_no' => $input['invoice_no'],
                    'discount_type' => $discount['discount_type'],
                    'discount_amount' => $this->productUtil->num_uf($input['discount_amount']),
                    'tax_id' => $input['tax_id'],
                    'tax_amount' => $invoice_total['tax'],
                    'total_before_tax' => $invoice_total['total_before_tax'],
                    'final_total' => $invoice_total['final_total'],
                    'is_pos_return' => 1,
                    'pos_invoice_return' => $input['pos_invoice_return']
                ];

                DB::beginTransaction();

                //Generate reference number
                if (empty($sell_return_data['invoice_no'])) {
                    //Update reference count
                    $ref_count = $this->productUtil->setAndGetReferenceCount('sell_return');
                    $sell_return_data['invoice_no'] = $this->productUtil->generateReferenceNumber('sell_return', $ref_count);
                }

                if (empty($sell_return)) {
                    $sell_return_data['business_id'] = $business_id;
                    $sell_return_data['location_id'] = $sell->location_id;
                    $sell_return_data['contact_id'] = $sell->contact_id;
                    $sell_return_data['customer_group_id'] = $sell->customer_group_id;
                    $sell_return_data['type'] = 'sell_return';
                    $sell_return_data['status'] = 'final';
                    $sell_return_data['created_by'] = $user_id;
                    $sell_return_data['return_parent_id'] = $sell->id;
                    $sell_return = Transaction::create($sell_return_data);
                } else {
                    $sell_return->update($sell_return_data);
                }

                if ($request->session()->get('business.enable_rp') == 1 && !empty($sell->rp_earned)) {
                    $is_reward_expired = $this->transactionUtil->isRewardExpired($sell->transaction_date, $business_id);
                    if (!$is_reward_expired) {
                        $diff = $sell->final_total - $sell_return->final_total;
                        $new_reward_point = $this->transactionUtil->calculateRewardPoints($business_id, $diff);
                        $this->transactionUtil->updateCustomerRewardPoints($sell->contact_id, $new_reward_point, $sell->rp_earned);

                        $sell->rp_earned = $new_reward_point;
                        $sell->save();
                    }
                }

                //Update payment status
                $this->transactionUtil->updatePaymentStatus($sell_return->id, $sell_return->final_total);

                //Update quantity returned in sell line
                $returns = [];
                $returns_variations = [];
                $product_lines = $request->input('products');
                foreach ($product_lines as $product_line) {
                    $returns[$product_line['sell_line_id']] = $product_line['quantity'];

                    $variation = TransactionSellLine::where('id', $product_line['sell_line_id'])->first();
                    if (!empty($variation) && $product_line['quantity'] > 0) {
                        $returns_variations[][$variation->variation_id] = $product_line['quantity'];
                    }
                }
                foreach ($sell->sell_lines as $sell_line) {
                    if (array_key_exists($sell_line->id, $returns)) {
                        $multiplier = 1;
                        if (!empty($sell_line->sub_unit)) {
                            $multiplier = $sell_line->sub_unit->base_unit_multiplier;
                        }

                        $quantity = $this->transactionUtil->num_uf($returns[$sell_line->id]) * $multiplier;

                        $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);
                        $quantity_formated = $this->transactionUtil->num_f($quantity);

                        $sell_line->quantity_returned = $quantity;
                        $sell_line->save();
                    }
                }

                $user_id = auth()->user()->id;
                $cash_regtister =  CashRegister::where('user_id', $user_id)
                    ->where('status', 'open')
                    ->first();
                if (!empty($cash_regtister)) {
                    $cash_regtister_transactiond_data = array(
                        'cash_register_id' => $cash_regtister->id,
                        'amount' => $invoice_total['final_total'],
                        'pay_method' => 'cash',
                        'type' => 'credit',
                        'transaction_type' => 'refund'
                    );
                    CashRegisterTransaction::create($cash_regtister_transactiond_data);
                }

                DB::commit();
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success'),
                    'amount' => $sell_return->final_total,
                    'returns' => $returns_variations
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = [
                'success' => 0,
                'msg' => $msg
            ];
        }

        return $output;
    }
}
