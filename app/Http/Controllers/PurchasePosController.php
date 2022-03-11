<?php

namespace App\Http\Controllers;

use App\Account;
use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\ContactGroup;
use App\TaxRate;
use App\Business;
use App\PurchaseLine;
use App\Store;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\TankPurchaseLine;

class PurchasePosController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $contactUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
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
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types($first_location, false, true);

        // no need thease methods in purchase page
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'supplier'; //contact type /used in quick add contact
        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('pos_create_data')->first();
        if (!empty($temp_data)) {
            $temp_data = json_decode($temp_data->pos_create_data); //name by mistake it is purchase
        }
        if (!request()->session()->get('business.popup_load_save_data')) {
            $temp_data = [];
        }


        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

        $purchase_no = $this->businessUtil->getFormNumber('purchase');

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;

        $default_location = BusinessLocation::where('business_id', $business_id)->first();
        $business_details = $this->businessUtil->getDetails($business_id);
        $default_supplier = $this->contactUtil->getDefaultSupplier($business_id);
        $payment_lines[] = $this->dummyPaymentLine;
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $enable_petro_module =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id, $enable_petro_module) : false;
        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::where('business_id', $business_id)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.all_brands'), 'all') : false;
        $default_datetime = $this->businessUtil->format_date('now', true);


        return view('purchase_pos.create')
            ->with(compact(
                'pos_settings',
                'payment_lines',
                'default_supplier',
                'business_details',
                'default_location',
                'cash_account_id',
                'purchase_no',
                'is_petro_enable',
                'tanks',
                'contact_id',
                'type',
                'temp_data',
                'taxes',
                'orderStatuses',
                'business_locations',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bank_group_accounts',
                'default_datetime',
                'categories',
                'brands'
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
        try {
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);

            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }
            $store_id = $request->input('store_id');
            $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'is_suspend', 'contact_id', 'transaction_date', 'purchase_entry_no', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['store_id'] = $request->input('store_id');
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $purchase_lines = [];
            $purchases = $request->input('purchases');

            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $store_id);
            //Add qty to sepcific tank if fuel category tank
            if (!empty($request->tanks)) {
                foreach ($request->tanks as $key => $tank) {
                    if (!empty($tank['qty'])) {
                        FuelTank::where('id', $key)->increment('current_balance', !empty($tank['qty']) ? $tank['qty'] : 0);
                        $product_id = FuelTank::where('id', $key)->first()->product_id;
                        TankPurchaseLine::create([
                            'business_id' => $business_id,
                            'transaction_id' => $transaction->id,
                            'tank_id' => $key,
                            'product_id' => $product_id,
                            'quantity' => !empty($tank['qty']) ? $tank['qty'] : 0,
                            'instock_qty' => !empty($tank['instock_qty']) ? $tank['instock_qty'] : 0,
                        ]);
                    }
                }
            }
            $payments = $request->input('payment');
            $due_remaining = 0;
            $total_paying = 0;
            foreach ($payments as $payment_arr) {
                $total_paying += $payment_arr['amount'];
            }
            if ($total_paying < $transaction->final_total) { // paying amount less then transaction total then pay by advance 
                $due_remaining =  $this->transactionUtil->adjustAdvancePayments($transaction, $payments[0]['amount'], $business_id);  //paid by advance amounts
            }
            if ($due_remaining > 0) {
                //set paid amount paid by advances and by user paying 
                $payments[0]['amount'] = $transaction->final_total - $due_remaining;  // reduce due amount from transaction total amount
            }
            if (!$transaction->is_suspend) {
                //Add Purchase payments
                $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            }

            //add stock account transactions
            $this->transactionUtil->manageStockAccount($transaction, [], 'debit', $transaction->final_total);

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
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
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $purchase = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(
                'contact',
                'payment_lines',
                'purchase_lines',
                'purchase_lines.product',
                'purchase_lines.product.unit',
                //'purchase_lines.product.unit.sub_units',
                'purchase_lines.variations',
                'purchase_lines.variations.product_variation',
                'location',
                'purchase_lines.sub_unit'
            )
            ->first();
        $location_id = $purchase->location_id;
        $business_location = BusinessLocation::find($location_id);
        $payment_types = $this->productUtil->payment_types($business_location);

        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types($first_location, false, true);

        // no need thease methods in purchase page
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'supplier'; //contact type /used in quick add contact
        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('pos_create_data')->first();
        if (!empty($temp_data)) {
            $temp_data = json_decode($temp_data->pos_create_data); //name by mistake it is purchase
        }
        if (!request()->session()->get('business.popup_load_save_data')) {
            $temp_data = [];
        }


        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

        $purchase_no = $this->businessUtil->getFormNumber('purchase');
        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;

        $default_location = BusinessLocation::where('business_id', $business_id)->first();
        $business_details = $this->businessUtil->getDetails($business_id);
        $default_supplier = $this->contactUtil->getDefaultSupplier($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $enable_petro_module =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id, $enable_petro_module) : false;
        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::where('business_id', $business_id)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.all_brands'), 'all') : false;
        $default_datetime = $this->businessUtil->format_date('now', true);

        if (!empty($purchase->payment_lines) && $purchase->payment_lines->count() > 0) {
            $payment_lines = $purchase->payment_lines;
        } else {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $hide_tax = 'hide';
        if (request()->session()->get('business.enable_inline_tax') == 1) {
            $hide_tax = '';
        }

        return view('purchase_pos.edit')
            ->with(compact(
                'purchase',
                'hide_tax',
                'stores',
                'pos_settings',
                'payment_lines',
                'default_supplier',
                'business_details',
                'default_location',
                'cash_account_id',
                'purchase_no',
                'is_petro_enable',
                'tanks',
                'contact_id',
                'type',
                'temp_data',
                'taxes',
                'orderStatuses',
                'business_locations',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bank_group_accounts',
                'default_datetime',
                'categories',
                'brands'
            ));
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
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;

            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only([
                'ref_no', 'status', 'contact_id',
                'transaction_date', 'total_before_tax',
                'discount_type', 'discount_amount', 'tax_id', 'is_suspend',
                'tax_amount', 'shipping_details',
                'shipping_charges', 'final_total',
                'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type'
            ]);

            $exchange_rate = $update_data['exchange_rate'];

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], true);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details);
            } else {
                $update_data['discount_amount'] = 0;
            }

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;
            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);
            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //Update transaction payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id);

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $transaction->store_id, $before_status);

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);


            if (!empty($request->tanks)) {
                foreach ($request->tanks as $key => $tank) {
                    if (!empty($tank['tank_purchase_line_id'])) {
                        $tank_purchase_line = TankPurchaseLine::findOrFail($tank['tank_purchase_line_id']);
                        $qty_difference = $tank['qty'] - $tank_purchase_line->quantity;

                        FuelTank::where('id', $key)->increment('current_balance', !empty($qty_difference) ? $qty_difference : 0);
                        $tank_purchase_line->quantity = $tank['qty'];
                        $tank_purchase_line->save();
                        $updated_purchase_line_ids[] = $tank_purchase_line->id;
                    } else {
                        FuelTank::where('id', $key)->increment('current_balance', !empty($tank['qty']) ? $tank['qty'] : 0);
                        $product_id = FuelTank::where('id', $key)->first()->product_id;
                        $tank_purchase_line = TankPurchaseLine::create([
                            'business_id' => $business_id,
                            'transaction_id' => $transaction->id,
                            'tank_id' => $key,
                            'product_id' => $product_id,
                            'quantity' => !empty($tank['qty']) ? $tank['qty'] : 0
                        ]);
                        $updated_purchase_line_ids[] = $tank_purchase_line->id;
                    }
                }
            }

            if (!empty($updated_purchase_line_ids)) {
                TankPurchaseLine::where('transaction_id', $transaction->id)
                    ->whereNotIn('id', $updated_purchase_line_ids)
                    ->delete();

                //update stock account transactions
                $this->transactionUtil->updateManageStockAccount($transaction);
            }
            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
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
}
