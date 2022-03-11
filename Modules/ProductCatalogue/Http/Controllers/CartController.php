<?php

namespace Modules\ProductCatalogue\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Customer;
use App\Product;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
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
        $location_id = request()->location_id;
        $business_id = request()->session()->get('business.id');
        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);
        $product_array = explode(',', request()->product_id_array);
        $products = Product::whereIn('id', $product_array)->with(['variations', 'variations.product_variation', 'category'])->get();


        return view('productcatalogue::catalogue.cart')->with(compact(
            'products',
            'business',
            'location_id',
            'business_location'
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

        $location_id = request()->location_id;
        $business_id = request()->session()->get('business.id');
        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);
        $total = $request->total_amount;
        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);


        return view('productcatalogue::catalogue.checkout')->with(compact(
            'total',
            'business',
            'contact_id',
            'business_location'
        ));
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
    /**
     * checkout
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4|max:255',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => 'Password does not match'
            ];
            return redirect()->back()->with('status', $output);
        }


        if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
        }


        $customer_data = array(
            'business_id' => $business_id,
            'first_name' => $request->name,
            'last_name' => '',
            'email' => $request->email,
            'username' => $request->contact_id,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'contact_number' => $request->alternate_number,
            'landline' => $request->landline,
            'geo_location' => '',
            'address' => $request->city,
            'town' => $request->state,
            'district' => $request->country,
            'is_company_customer' => 1
        );

        Customer::create($customer_data);


        $input = $request->only([
            'name',  'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'contact_id', 'email'
        ]);

        $input['type'] = 'customer';
        $input['business_id'] = $business_id;
        $input['created_by'] = $request->session()->get('user.id');

        $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
        if ($request->transaction_date && $request->type == 'supplier') {
            $input['created_at'] = date('Y-m-d H:i:s', strtotime($request->transaction_date));
        }
        //Check Contact id
        $count = 0;
        if (!empty($input['contact_id'])) {
            $count = Contact::where('business_id', $input['business_id'])
                ->where('contact_id', $input['contact_id'])
                ->count();
        }

        if ($count == 0) {
            //Update reference count
            $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

            if (empty($input['contact_id'])) {
                //Generate reference number
                $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
            }


            $contact = Contact::create($input);


        } else {
            throw new \Exception("Error Processing Request", 1);
        }

        return 'success';
    }
}
