<?php

namespace App\Http\Controllers\Ecom;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Account;
use App\AccountType;
use App\Brands;
use App\Business;
use App\BusinessCategory;
use App\Media;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\Currency;
use App\ContactGroup;
use App\CustomerPendingPayment;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\System;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TypesOfService;
use App\UploadedOrder;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

class EcomCustomerOrderController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $notificationUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, NotificationUtil $notificationUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = [
            'method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_categories = BusinessCategory::pluck('category_name', 'id');
        $countries = DB::table('countries')->pluck('country', 'country');

        $business_locations = [];
        if (Auth::user()->is_company_customer) {
            $business_id = Auth::user()->business_id;
            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        }

        return view('ecom_customer.order.order')->with(compact('business_categories', 'countries', 'business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->business_id;

        $contact_id = $this->getOrCreateContact(Auth::user()->username, $business_id);

        if (request()->order_mode == 'by_upload_document_image' && !empty($business_id)) {

            return redirect(action('Ecom\EcomCustomerOrderController@uploadDocumentImage', $business_id));
        }

        return redirect()->to(action('Ecom\EcomCustomerOrderController@createPos', request()->all()));
    }

    public function createPos(Request $request)
    {
        $business_id = request()->business_id;
        if (Auth::user()->is_company_customer) {
            $location_id = request()->location_id;
        } else {
            $location_id = BusinessLocation::where('business_id', $business_id)->where('city', request()->city)->first()->id;
        }
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $payment_lines[] = $this->dummyPaymentLine;

        $default_location = BusinessLocation::findOrFail($location_id);

        $payment_types = $this->productUtil->payment_types($default_location);

        //Shortcuts
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $search_product_settings = empty($business_details->search_product_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->search_product_settings, true);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        //If brands, category are enabled then send else false.
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : false;
        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::where('business_id', $business_id)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.all_brands'), 'all') : false;

        $change_return = $this->dummyPaymentLine;

        $types = Contact::getContactTypes();
        $customer_groups = ContactGroup::forDropdown($business_id);

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        if ($this->moduleUtil->isModuleEnabled('kitchen')) {
            $waiters = $this->transactionUtil->serviceStaffDropdown($business_id);
        } else {
            $waiters = '';
        }
        $default_price_group_id = !empty($default_location->selling_price_group_id) ? $default_location->selling_price_group_id : null;

        //Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }
        $waiter_enable = $this->moduleUtil->isModuleEnabled('kitchen');
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $default_datetime = $this->businessUtil->format_date('now', true);
        $contact_id = Contact::where('business_id', $business_id)->where('contact_id', Auth::user()->username)->first()->id;
        $temp_data = [];

        $patients = [];
        if (request()->session()->get('business.is_pharmacy')) {
            $patients = Business::where('is_patient', 1)->pluck('name', 'id');
        }
        $user = User::where('id', Auth::user()->id)->select('toggle_popup')->first();
        if (!empty($user)) {
            $toggle_popup = $user->toggle_popup;
        } else {
            $toggle_popup = 1;
        }

        return view('ecom_customer.order.pos')
            ->with(compact(
                'toggle_popup',
                'patients',
                'business_details',
                'taxes',
                'payment_types',
                'walk_in_customer',
                'payment_lines',
                'default_location',
                'shortcuts',
                'commission_agent',
                'categories',
                'brands',
                'pos_settings',
                'change_return',
                'types',
                'customer_groups',
                'accounts',
                'price_groups',
                'types_of_service',
                'default_price_group_id',
                'shipping_statuses',
                'default_datetime',
                'waiters',
                'waiter_enable',
                'search_product_settings',
                'temp_data',
                'contact_id',
                'business_id'
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
        $business_id = $request->business_id;

        $is_direct_sale = false;
        if (!empty($request->input('is_direct_sale'))) {
            $is_direct_sale = true;
        }

        try {
            $input = $request->except('_token');
            //Check Customer credit limit
            $is_credit_limit_exeeded = $this->transactionUtil->isCustomerCreditLimitExeeded($input);

            if ($is_credit_limit_exeeded !== false) {
                $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
                $output = [
                    'success' => 0,
                    'msg' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
                ];
                if (!$is_direct_sale) {
                    return $output;
                } else {
                    return redirect()
                        ->action('SellController@index')
                        ->with('status', $output);
                }
            }

            $input['is_quotation'] = 0;
            //status is send as quotation from Add sales screen.
            if ($input['status'] == 'quotation') {
                $input['status'] = 'draft';
                $input['is_quotation'] = 1;
            }

            if (!empty($input['products'])) {

                //Check if subscribed or not, then check for users quota
                if (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
                }
                $contact_id = $request->get('contact_id', null);
                $user_id = Auth::user()->id;

                $discount = [
                    'discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount']
                ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

                DB::beginTransaction();

                if (empty($request->input('transaction_date'))) {
                    $input['transaction_date'] =  \Carbon::now();
                } else {
                    $input['transaction_date'] = $this->productUtil->uf_date($request->input('transaction_date'), true);
                }
                if ($is_direct_sale) {
                    $input['is_direct_sale'] = 1;
                }

                //Set commission agent
                $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');
                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }

                if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                    $input['exchange_rate'] = 1;
                }

                //Customer group details
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                //set selling price group id
                $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

                //If default price group for the location exists
                $price_group_id = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;

                $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;
                if ($input['is_suspend']) {
                    $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                }

                //Generate reference number
                if (!empty($input['is_recurring'])) {
                    //Update reference count
                    $ref_count = $this->transactionUtil->setAndGetReferenceCount('subscription');
                    $input['subscription_no'] = $this->transactionUtil->generateReferenceNumber('subscription', $ref_count);
                }

                if ($is_direct_sale) {
                    $input['invoice_scheme_id'] = $request->input('invoice_scheme_id');
                }

                //Types of service
                if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                    $input['types_of_service_id'] = $request->input('types_of_service_id');
                    $price_group_id = !empty($request->input('types_of_service_price_group')) ? $request->input('types_of_service_price_group') : $price_group_id;
                    $input['packing_charge'] = !empty($request->input('packing_charge')) ?
                        $this->transactionUtil->num_uf($request->input('packing_charge')) : 0;
                    $input['packing_charge_type'] = $request->input('packing_charge_type');
                    $input['service_custom_field_1'] = !empty($request->input('service_custom_field_1')) ?
                        $request->input('service_custom_field_1') : null;
                    $input['service_custom_field_2'] = !empty($request->input('service_custom_field_2')) ?
                        $request->input('service_custom_field_2') : null;
                    $input['service_custom_field_3'] = !empty($request->input('service_custom_field_3')) ?
                        $request->input('service_custom_field_3') : null;
                    $input['service_custom_field_4'] = !empty($request->input('service_custom_field_4')) ?
                        $request->input('service_custom_field_4') : null;
                }

                $input['selling_price_group_id'] = $price_group_id;
                $input['is_customer_order'] = 1;
                $input['order_status'] = 'ordered';
                $input['is_quotation'] = 1;

                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);


                if (!$is_direct_sale) {
                    //Add change return
                    if ($request->is_pos != 1) {
                        $change_return = $this->dummyPaymentLine;
                        $change_return['amount'] = $input['change_return'];
                        $change_return['is_return'] = 1;
                        $input['payment'][] = $change_return;
                    }
                }
                $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

                if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);
                }
                /* ------------Account Transaction--------------- */
                if ($is_credit_sale) {
                    $acc_id =  $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                    $this->createAccountTransaction($transaction, 'debit', $acc_id);
                }

                $update_transaction = false;
                if ($this->transactionUtil->isModuleEnabled('tables')) {
                    $transaction->res_table_id = request()->get('res_table_id');
                    $update_transaction = true;
                }
                if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                    $transaction->res_waiter_id = request()->get('res_waiter_id');
                    $update_transaction = true;
                }
                if ($update_transaction) {
                    $transaction->save();
                }

                //Check for final and do some processing.
                if ($input['status'] == 'final') {
                    //update product stock
                    foreach ($input['products'] as $product) {
                        $decrease_qty = $this->productUtil
                            ->num_uf($product['quantity']);
                        if (!empty($product['base_unit_multiplier'])) {
                            $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                        }

                        if ($product['enable_stock']) {
                            $this->productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $input['location_id'],
                                $decrease_qty
                            );
                        }

                        if ($product['product_type'] == 'combo') {
                            //Decrease quantity of combo as well.
                            $this->productUtil
                                ->decreaseProductQuantityCombo(
                                    $product['combo'],
                                    $input['location_id']
                                );
                        }
                    }

                    //Add payments to Cash Register
                    if (!$is_direct_sale && !$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                        $this->cashRegisterUtil->addSellPayments($transaction, $input['payment']);
                    }

                    //Update payment status
                    $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

                    if ($request->session()->get('business.enable_rp') == 1) {
                        $redeemed = !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0;
                        $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, 0, $redeemed);
                    }

                    //Allocate the quantity from purchase and add mapping of
                    //purchase & sell lines in
                    //transaction_sell_lines_purchase_lines table
                    $business_details = $this->businessUtil->getDetails($business_id);
                    $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

                    $business = [
                        'id' => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id' => $input['location_id'],
                        'pos_settings' => $pos_settings
                    ];
                    $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

                    //Auto send notification
                    $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
                }

                //Set Module fields
                if (!empty($input['has_module_data'])) {
                    $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);
                }

                Media::uploadMedia($business_id, $transaction, $request, 'documents');

                DB::commit();


                $output = ['success' => 1, 'msg' => __('customer.order_created_sccuess')];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $msg = trans("messages.something_went_wrong");

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            $output = [
                'success' => 0,
                'msg' => $msg
            ];
        }

        return $output;
    }


    public function getOrCreateContact($customer_username, $business_id)
    {
        $contact_exist = Contact::where('contact_id', $customer_username)->where('business_id', $business_id)->first();
        if (!empty($contact_exist)) {
            return $contact_exist->id;
        } else {
            $contact_old = Auth::user();
            $contact_data = array(
                'business_id' => $business_id,
                'type' => 'customer',
                'name' => $contact_old->first_name,
                'email' => $contact_old->email,
                'contact_id' => $contact_old->username,
                'city' => $contact_old->town,
                'address' => $contact_old->address,
                'geo_location' => $contact_old->geo_location,
                'state' => $contact_old->district,
                'mobile' => $contact_old->mobile,
                'landline' => $contact_old->landline,
                'alternate_number' => $contact_old->contact_number,
                'created_by' => $contact_old->id
            );

            $contact = Contact::create($contact_data);

            return $contact->id;
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
        $trans = Transaction::where('id', $id)->first();
        $business_id = $trans->business_id;
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['contact', 'sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties']);

        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }

        $sell = $query->firstOrFail();

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $payment_types = $this->transactionUtil->payment_types();

        $order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $shipping_status_colors = $this->shipping_status_colors;
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;

        return view('sale_pos.show')
            ->with(compact(
                'taxes',
                'sell',
                'payment_types',
                'order_taxes',
                'pos_settings',
                'shipping_statuses',
                'shipping_status_colors',
                'is_warranty_enabled'
            ));
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
     * Getting the order created by customer
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function getOrders()
    {
        if (request()->ajax()) {
            $is_quotation = request()->only('is_quotation', 0);

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('business', 'transactions.business_id', '=', 'business.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'draft')
                ->orWhere('transactions.status', 'final')
                ->where('is_customer_order', 1)
                ->where('contacts.contact_id', auth()->user()->username)
                ->select(
                    'transactions.id',
                    'transactions.business_id',
                    'transactions.order_status',
                    'transactions.payment_status',
                    'transactions.is_customer_order',
                    'transaction_date',
                    'invoice_no',
                    'business.name',
                    'bl.name as business_location',
                    'is_direct_sale'
                );

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $customer_to_directly_in_panel = $this->moduleUtil->hasThePermissionInSubscription($row->business_id, 'customer_to_directly_in_panel');
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        $html .=  '<li><a href="#" data-href="' . action("Ecom\EcomCustomerOrderController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i>' . __("messages.view") . '</a> </li>';
                        if ($row->order_status == "waiting_for_confirmation") {
                            $html .= '<li><a data-href="' . action("Ecom\EcomCustomerOrderController@confirmOrder", [$row->id]) . '" class="confirm_order"><i class="fa fa-check"></i> ' . __("lang_v1.confirm") . '</a> <li>';
                        }
                        if ($row->order_status == "invoiced" && $row->payment_status != 'pending' && $row->payment_status != 'paid' && $customer_to_directly_in_panel == 1) {
                            $html .= '<li><a href="' . action('Ecom\EcomCustomerOrderController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.add_payment") . '</a></li>';
                        }

                        if ($row->payment_status == "pending") {
                            $html .= '<li><a href="' . action('TransactionPaymentController@pendingPayment', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("lang_v1.pending_payment_confimation") . '</a></li>';
                        }
                        // $html .= '<button data-href="'.action("Ecom\EcomCustomerOrderController@makeTheBill", [$row->id]).'" class="btn btn-xs btn-danger make_the_bill"><i class="fa fa-file-o"></i> '. __("lang_v1.make_the_bill").'</button>';
                        // $html .= '<!-- &nbsp; <a href="'.action("SellPosController@destroy", [$row->id].'" class="btn btn-xs btn-danger delete-sale"><i class="fa fa-trash"></i>  '. __("messages.delete") .'</a>  -->';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{$transaction_date}}')
                ->editColumn(
                    'order_status',
                    '<span class="label @order_status($order_status)">{{__(\'lang_v1.\' . $order_status)}}
                        </span></span>'
                )
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('Ecom\EcomCustomerOrderController@show', [$row->id]);
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'order_status'])
                ->make(true);
        }
        return view('ecom_customer.order.index');
    }

    /**
     * Getting the order created by customer
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function getUploadedOrders()
    {
        if (request()->ajax()) {
            $is_quotation = request()->only('is_quotation', 0);
            $uploaded_orders = UploadedOrder::leftJoin('contacts', 'uploaded_orders.contact_id', 'contacts.contact_id')
                ->leftJoin('business', 'uploaded_orders.business_id', 'business.id')
                ->leftJoin('transactions', 'uploaded_orders.transaction_id', 'transactions.id')

                ->where('contacts.contact_id', auth()->user()->username)
                ->select(
                    'uploaded_orders.*',
                    'transactions.order_status',
                    'uploaded_orders.created_at as transaction_date',
                    'invoice_no',
                    'business.name'
                );

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $uploaded_orders->whereDate('uploaded_orders.created_at', '>=', $start)
                    ->whereDate('uploaded_orders.created_at', '<=', $end);
            }


            return Datatables::of($uploaded_orders)
                ->addColumn(
                    'action',
                    '<a href="#" data-href="{{action(\'Ecom\EcomCustomerOrderController@getImage\', [$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a>
                    '
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{$transaction_date}}')
                ->editColumn(
                    'order_status',
                    '<span class="label @order_status($order_status)">{{__(\'lang_v1.\' . $order_status)}}
                        </span></span>'
                )
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('Ecom\EcomCustomerOrderController@getImage', [$row->id]);
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'order_status'])
                ->make(true);
        }
        return view('ecom_customer.order.uploaded_order');
    }

    public function confirmOrder($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->order_status = 'confirmed';
            $transaction->save();
            //Auto send notification
            $this->notificationUtil->autoSendNotification($transaction->business_id, 'order_confirmed', $transaction, $transaction->contact);
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.order_confirmed_successfuly')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }


    public function uploadDocumentImage($business_id)
    {
        return view('ecom_customer.order.upload_document_image')->with(compact('business_id'));
    }

    public function uploadDocumentImageSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'upload_document_image' => 'required|mimes:jpeg,png,bmp|max:4096'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }

        try {
            $business_id = $request->business_id;
            $contact_name = Auth::user()->username;

            //upload prfile image
            if (!file_exists('./public/img/upload_orders/' . $contact_name)) {
                mkdir('./public/img/upload_orders/' . $contact_name, 0777, true);
            }
            if ($request->hasfile('upload_document_image')) {

                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');

                $file = $request->file('upload_document_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/upload_orders/' . $contact_name . '/' . $filename);
                $uploadFileFicon = 'img/upload_orders/' . $contact_name . '/' . $filename;
            }

            $contact_id = $this->getOrCreateContact(Auth::user()->username, $business_id);

            $data = array(
                'contact_id' => Auth::user()->username,
                'business_id' => $business_id,
                'image' => $uploadFileFicon
            );

            UploadedOrder::create($data);

            $output = [
                'success' => true,
                'msg' => __('customer.order_upload_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('/customer/order/uploaded')->with('status', $output);
    }


    public function getImage($id)
    {
        $order_uploaded = UploadedOrder::findOrFail($id);
        $image_url = $order_uploaded->image;

        return view('ecom_customer.order.get_image')->with(compact('image_url'));
    }


    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($transaction_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('id', $transaction_id)
                ->where('business_id', $business_id)
                ->with(['contact', 'location'])
                ->first();
            if ($transaction->payment_status != 'paid') {
                $payment_types = ['direct_bank_deposit' => __('lang_v1.direct_bank_deposit')];

                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);
                $amount = $transaction->final_total - $paid_amount;
                if ($amount < 0) {
                    $amount = 0;
                }

                $amount_formated = $this->transactionUtil->num_f($amount);

                $payment_line = new TransactionPayment();
                $payment_line->amount = $amount;
                $payment_line->method = 'bank_transfer';
                $payment_line->paid_on = Carbon::now()->toDateTimeString();

                $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();
                $account_module = $this->moduleUtil->isModuleEnabled('account');
                if ($transaction->type == 'expense') {
                    if ($this->moduleUtil->isModuleEnabled('account')) {
                        $accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_account_type_id->id)->notClosed()->pluck('name', 'id');
                    }
                } else {
                    //Accounts
                    $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
                }

                $view = view('ecom_customer.order.partials.payment_row')
                    ->with(compact('transaction', 'transaction_id', 'payment_types', 'account_module', 'payment_line', 'amount_formated', 'accounts'))->render();

                $output = [
                    'status' => 'due',
                    'view' => $view
                ];
            } else {
                $output = [
                    'status' => 'paid',
                    'view' => '',
                    'msg' => __('purchase.amount_already_paid')
                ];
            }

            return json_encode($output);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $transaction_id = $request->input('transaction_id');
            $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);


            // $data = array(
            //     'transaction_id' => $transaction_id,
            //     'request_data' => $request->except('_token'),
            // );

            // CustomerPendingPayment::create($data);

            TransactionPayment::where('transaction_id', $transaction_id)->where('amount', 0)->delete();

            if ($transaction->payment_status != 'paid') {
                $inputs = $request->only([
                    'amount', 'method', 'note', 'card_number', 'card_holder_name',
                    'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                    'cheque_number', 'bank_account_number'
                ]);
                $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
                $inputs['transaction_id'] = $transaction->id;
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['payment_for'] = $transaction->contact_id;

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

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
                $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                $inputs['is_return'] = !empty($request->is_return) ? $request->is_return : 0; //added by ahmed
                $tp = TransactionPayment::create($inputs);

                //update payment status
                $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                // $inputs['transaction_type'] = $transaction->type;
                // event(new TransactionPaymentAdded($tp, $inputs));

                $transaction->payment_status = 'pending';
                $transaction->save();

                DB::commit();
            }

            $output = [
                'success' => true,
                'msg' => __('purchase.payment_added_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}
