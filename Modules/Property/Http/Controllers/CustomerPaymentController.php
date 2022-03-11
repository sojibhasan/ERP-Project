<?php

namespace Modules\Property\Http\Controllers;

use App\Contact;
use App\Account;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PaymentOption;

class CustomerPaymentController extends Controller
{
    protected $moduleUtil;
    protected $commonUtil;
    protected $businessUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'bank_name' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('property::customer');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $payment_options = PaymentOption::where('business_id', $business_id)->pluck('payment_option', 'id');
        $layout = 'property';
        $customers = Contact::propertyCustomerDropdown($business_id, false, true);
        $payment_types = $this->commonUtil->payment_types();
        $land_and_blocks = Property::getLandAndBlockDropdown($business_id, true, true);
        
        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
        $payment = $this->dummyPaymentLine;
        
        return view('property::customer_payment.create')->with(compact(
            'layout',
            'payment_options',
            'customers',
            'payment',
            'payment_types',
            'bank_group_accounts',
            'land_and_blocks',
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('property::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('property::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
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
     * get property dropddown by customer id
     * @param int $customer_id
     * @return Renderable
     */
    public function getPropertyDropdownByCustomer($customer_id){
        $properties = Property::getLandAndBlockByCustomerDropdown($customer_id, true, true);

        return $this->transactionUtil->createDropdownHtml($properties, 'Please Select');
    }
    
}
