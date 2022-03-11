<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Business;
use App\BusinessCategory;
use App\BusinessLocation;
use App\CompanyPackageVariable;
use App\Product;
use App\Transaction;
use App\User;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\VariationLocationDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Superadmin\Entities\GiveAwayGift;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Spatie\Permission\Models\Permission;

use function GuzzleHttp\json_decode;

class BusinessController extends BaseController
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }



        $business = Business::select(DB::raw("CONCAT(COALESCE(name, ''),' ',COALESCE(company_number, '')) AS company_name"), 'id')->get();

        $date_today = \Carbon::today();

        $businesses = Business::orderby('name')
            ->with(['subscriptions' => function ($query) use ($date_today) {
                $query->whereDate('start_date', '<=', $date_today)
                    ->whereDate('end_date', '>=', $date_today);
            }, 'locations', 'owner'])
            ->paginate(21);

        if ($request->post() && $request->filter_business != 0) {
            $filter_business = $request->filter_business;
            $businesses = Business::orderby('name')
                ->with(['subscriptions' => function ($query) use ($date_today) {
                    $query->whereDate('start_date', '<=', $date_today)
                        ->whereDate('end_date', '>=', $date_today);
                }, 'locations', 'owner'])
                ->where('id', $filter_business)
                ->paginate(21);
        }


        $business_id = request()->session()->get('user.business_id');
        return view('superadmin::business.index')
            ->with(compact('businesses', 'business_id', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();
        $business_categories = BusinessCategory::pluck('category_name', 'id');

        $accounting_methods = $this->businessUtil->allAccountingMethods();

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $is_admin = true;

        $show_give_away_gift_in_register_page = !empty(System::getProperty('show_give_away_gift_in_register_page')) ? System::getProperty('show_give_away_gift_in_register_page') : [];
        $show_referrals_in_register_page = !empty(System::getProperty('show_referrals_in_register_page')) ? System::getProperty('show_referrals_in_register_page') : [];
        $show_give_away_gift_in_register_page = json_decode($show_give_away_gift_in_register_page, true);
        $show_referrals_in_register_page = json_decode($show_referrals_in_register_page, true);
        $give_away_gifts_array = GiveAwayGift::pluck('name', 'id')->toArray();

        $give_away_gifts = [];
        foreach ($give_away_gifts_array as $key => $value) {
            $give_away_gifts[$key] = $value;
        }

        return view('superadmin::business.create')
            ->with(compact(
                'currencies',
                'timezone_list',
                'business_categories',
                'accounting_methods',
                'show_referrals_in_register_page',
                'months',
                'is_admin',
                'show_give_away_gift_in_register_page',
                'show_referrals_in_register_page',
                'give_away_gifts'
            ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
            $owner_details['language'] = env('APP_LOCALE');

            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'time_zone', 'accounting_method', 'fy_start_month']);

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
            $business_details['business_categories'] = !empty($request->business_categories) ? json_encode($request->business_categories) : json_encode([]);

            $business_details['show_for_customers'] = !empty($request->show_for_customers) ? $request->show_for_customers : 0;
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            //add default account and account types for business
            app('App\Http\Controllers\BusinessController')->addAccounts($business->id);
            //add petro fuel category for business
            $this->businessUtil->addPetroDefaults($business->id, $user->id);


            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //set defualt number of pumps for location
            ModulePermissionLocation::create(['business_id' => $business->id, 'module_name' => 'number_of_pumps', 'locations' => [$new_location->id => '12']]);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);

            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            $output = [
                'success' => 1,
                'msg' => __('business.business_created_succesfully')
            ];

            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\BusinessController@index')
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return back()->with('status', $output)->withInput();
        }
    }
    /**
     * Store a newly created hospital resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function hospital_register(Request $request)
    {
        $startingHospitalPrefix = System::getProperty('hospital_prefix');
        try {
            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
            $owner_details['language'] = env('APP_LOCALE');

            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'time_zone', 'accounting_method', 'fy_start_month']);

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);

            //adding hospital business name prefix
            $business_details['name'] = $startingHospitalPrefix . '' . $business_details['name'];
            $business_details['is_hospital'] = 1;
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            //add default account and account types for business
            // app('App\Http\Controllers\BusinessController')->addAccounts($business->id);


            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);

            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            $hospitals = Business::where('is_hospital', 1)->select('id', 'name')->get();

            $output = [
                'success' => 1,
                'msg' => __('patient.hospital_add_success'),
                'hospitals' => $hospitals
            ];

            return $output;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }
    /**
     * Store a newly created pharmacy resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function pharmacy_register(Request $request)
    {
        $startingPharmacyPrefix = System::getProperty('pharmacy_prefix');
        try {
            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
            $owner_details['language'] = env('APP_LOCALE');

            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'time_zone', 'accounting_method', 'fy_start_month']);

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);

            //adding hospital business name prefix
            $business_details['name'] = $startingPharmacyPrefix . '' . $business_details['name'];
            $business_details['is_pharmacy'] = 1;
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            //add default account and account types for business
            // app('App\Http\Controllers\BusinessController')->addAccounts($business->id);


            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);

            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            $hospitals = Business::where('is_pharmacy', 1)->select('id', 'name')->get();

            $output = [
                'success' => 1,
                'msg' => __('patient.pharmacy_add_success'),
                'hospitals' => $hospitals
            ];

            return $output;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }

    /**
     * Store a newly created laboratry resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function laboratory_register(Request $request)
    {
        $startingLaboratoryPrefix = System::getProperty('laboratory_prefix');
        try {
            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
            $owner_details['language'] = env('APP_LOCALE');

            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'time_zone', 'accounting_method', 'fy_start_month']);

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);

            //adding hospital business name prefix
            $business_details['name'] = $startingLaboratoryPrefix . '' . $business_details['name'];
            $business_details['is_laboratory'] = 1;
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            //add default account and account types for business
            // app('App\Http\Controllers\BusinessController')->addAccounts($business->id);


            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);

            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            $hospitals = Business::where('is_laboratory', 1)->select('id', 'name')->get();

            $output = [
                'success' => 1,
                'msg' => __('patient.laboratory_add_success'),
                'hospitals' => $hospitals
            ];

            return $output;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($business_id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $business = Business::with(['currency', 'locations', 'subscriptions', 'owner'])->find($business_id);

        $created_id = $business->created_by;

        $created_by = !empty($created_id) ? User::find($created_id) : null;

        return view('superadmin::business.show')
            ->with(compact('business', 'created_by'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('superadmin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->businessUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Check if logged in busines id is same as deleted business then not allowed.
            $business_id = request()->session()->get('user.business_id');
            if ($business_id == $id) {
                $output = ['success' => 0, 'msg' => __('superadmin.lang.cannot_delete_current_business')];
                return back()->with('status', $output);
            }

            DB::beginTransaction();

            //Delete related products & transactions.
            $products_id = Product::where('business_id', $id)->pluck('id')->toArray();
            if (!empty($products_id)) {
                VariationLocationDetails::whereIn('product_id', $products_id)->delete();
            }
            Transaction::where('business_id', $id)->delete();

            Business::where('id', $id)
                ->delete();

            DB::commit();

            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\BusinessController@index')
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return back()->with('status', $output)->withInput();
        }
    }

    /**
     * Changes the activation status of a business.
     * @return Response
     */
    public function toggleActive(Request $request, $business_id, $is_active)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->businessUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        Business::where('id', $business_id)
            ->update(['is_active' => $is_active]);

        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return back()->with('status', $output);
    }

    /**
     * custom business package permissions
     * @return Response
     */
    public function manage($id, Request $request)
    {

        $business = Business::where('id', $id)->first();
        $currencies = $this->businessUtil->allCurrencies();
        $package_manage = Package::where('only_for_business', $id)->first();

        $subscription = Subscription::active_subscription($id);
        $previous_package_data['product_count'] = 0;
        $previous_package_data['location_count'] = 0;
        $previous_package_data['vehicle_count'] = 0;
        if (!empty($subscription) && empty($package_manage)) {
            // if conpmany package is not there then get the already subscribed package
            $already_running_pacakge = Package::where('id', $subscription->package_id)->first();
            $previous_package_data['product_count'] = $subscription->package_details['product_count'];
            $previous_package_data['location_count'] = $subscription->package_details['location_count'];
            $previous_package_data['vehicle_count'] = $subscription->package_details['vehicle_count'];
        }

        $module_enable_price = !empty($package_manage->module_enable_price) ? json_decode($package_manage->module_enable_price) : []; //input values
        if (!empty($subscription)){
            $manage_module_enable = $subscription->package_details;
        }else{
            $manage_module_enable = !empty($package_manage->manage_module_enable) ? json_decode($package_manage->manage_module_enable) : []; //checkboxes
        }

        // return $manage_module_enable;
        $other_permissions = !empty($package_manage->other_permissions) ? json_decode($package_manage->other_permissions) : []; //checkboxes
        $current_values = !empty($package_manage->current_values) ? ($package_manage->current_values) : [];
        $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
        $business_locations = BusinessLocation::where('business_id', $id)->get();

        $module_permission_locations = ModulePermissionLocation::getModulePermissionList();

        $business_details = Business::where('id', $id)->first();
        $sale_import_date = null;
        $purchase_import_date = null;
        if (!empty($business_details)) {
            $sale_import_date = !empty($business_details->sale_import_date) ? $this->moduleUtil->format_date($business_details->sale_import_date) : null;
        }
        if (!empty($business_details)) {
            $purchase_import_date = !empty($business_details->purchase_import_date) ? $this->moduleUtil->format_date($business_details->purchase_import_date) : null;
        }

        $module_permission_locations_value = [];
        foreach ($module_permission_locations as $module) {
            $module_permission_locations_value[$module] = ModulePermissionLocation::getModulePermissionLocations($id, $module);
        }

        $payment_types = $this->moduleUtil->payment_types();
        $accounts = Account::where('business_id', $id)->get();
        $business_locations = BusinessLocation::where('business_id', $id)->get();

        $only_assets_accounts = Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')->where('accounts.business_id', $id)->where(function ($query) {
            $query->where('account_types.name', 'Current Assets');
        })->pluck('accounts.name', 'accounts.id');
        $business_locations = BusinessLocation::where('business_id', $id)->get();

        $customer_interest_deduct_option = $business->customer_interest_deduct_option;
        $other_permissions_array = array(
            'purchase',
            'stock_transfer',
            'service_staff',
            'enable_subscription',
            'add_sale',
            'stock_adjustment',
            'tables',
            'type_of_service',
            'pos_sale',
            'expenses',
            'modifiers',
            'kitchen',
            'customer_interest_deduct_option'
        );
        return view('superadmin::business.manage')->with(compact('only_assets_accounts', 'business_locations', 'payment_types', 'previous_package_data', 'business_details', 'accounts', 'business_locations', 'sale_import_date', 'purchase_import_date', 'module_permission_locations', 'module_permission_locations_value', 'other_permissions', 'other_permissions_array', 'id', 'business', 'currencies', 'package_manage', 'module_enable_price', 'current_values', 'manage_module_enable', 'sms_settings','customer_interest_deduct_option'));
    }

    /**
     * save manage permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveManage($id, Request $request)
    {
        //return $request;
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required',
            'annual_fee_package' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }
        try {
            $business = Business::findOrFail($id);
            $subscription = Subscription::active_subscription($id);
            $already_running_pacakge = null;
            if (!empty($subscription)) {
                $already_running_pacakge = Package::where('id', $subscription->package_id)->first();
            }
            $business_details = $request->only(['sms_settings']);
            if (!empty($business_details) &&  $request->input('access_sms_settings') == 1) {
                $business->fill($business_details);
                $business->save();
            }
            $validator = Validator::make($request->all(), [
                'annual_fee_package' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => __('superadmin::lang.annaul_fee_package_requried')
                ];
                return redirect()->back()->with('status', $output);
            }

            //set default account mapping for locations
            foreach ($request->default_payment_accounts as $key => $default_payment_account) {
                BusinessLocation::where('id', $key)->update(['default_payment_accounts' => $default_payment_account]);
            }

            //remove all previous accounting value for business
            if (!empty($request->zero_previous_accounting_values) && $request->zero_previous_accounting_values == 1) {
                $this->deleteAllPreviousAccountTransactions($id);
            }
            //hide accounts if superadmin not checked
            $business_accounts = Account::where('business_id', $id)->get();
//            $accounts_enabled =  $request->accounts_enabled;
//            foreach ($business_accounts as $value) {
//                Account::where('id', $value->id)->update(['visible' => array_key_exists($value->id, $accounts_enabled) ? 1 : 0]);
//            }

            //set location of module permission allowed
            if (!empty($request->module_permission_location)) {
                foreach ($request->module_permission_location as $key => $prems) {
                    $module_permission_location_data = array(
                        'business_id' => $id,
                        'module_name' => $key,
                        'locations' => $prems
                    );

                    ModulePermissionLocation::updateOrCreate(['business_id' => $id, 'module_name' => $key], $module_permission_location_data);
                }
            }

            //save sale_import_date to business
            $sale_import_date = $request->sale_import_date;
            $purchase_import_date = $request->purchase_import_date;
            $sale_date = null;
            $purchase_date = null;
            if (!empty($sale_import_date)) {
                $sale_date = Carbon::parse($sale_import_date)->format('Y-m-d');
            }
            if (!empty($purchase_import_date)) {
                $purchase_date = Carbon::parse($purchase_import_date)->format('Y-m-d');
            }
            Business::where('id', $id)->update(['sale_import_date' => $sale_date, 'purchase_import_date' => $purchase_date]);

            $annual_fee_package = $request->annual_fee_package;
            $moudle_permissions = Subscription::getBusinessPermissionsArray();

            $module_enable_price = [];
            $manage_module_enable = [];
            $moudles = $request->only($moudle_permissions);
            if (!empty($subscription)) {
                $package_details = $subscription->package_details;
            } else {
                $package_details = [];
            }
            foreach ($moudle_permissions as $permission) {
                if (array_key_exists($permission, $moudles)) {
                    if ($moudles[$permission]) { //module has amount value
                        $module_enable_price[$permission] = (float) $request->input($permission . '_value'); //input values
                        $manage_module_enable[$permission] = $request->input($permission); //checkboxes

                        $package_details[$permission] = 1;
                        $package_details['manufacturing_module'] = !empty($manage_module_enable['mf_module']) ? $manage_module_enable['mf_module'] : 0;
                        $package_details['enable_petro_module'] = 1;

                        if (!empty($subscription)) {
                            Subscription::where('id', $subscription->id)->update(['package_details->' . $permission => '1']);
                        }
                    }
                } else {
                    if (!empty($subscription)) {
                        Subscription::where('id', $subscription->id)->update(['package_details->' . $permission => '0']);
                    }
                }
            }
            $other_permissions_array = array(
                'purchase',
                'stock_transfer',
                'service_staff',
                'enable_subscription',
                'add_sale',
                'stock_adjustment',
                'tables',
                'type_of_service',
                'pos_sale',
                'expenses',
                'modifiers',
                'kitchen',
                'customer_interest_deduct_option'
            );
            $other_permissions = [];

            foreach ($other_permissions_array as $value) {
                if (!empty($request->$value)) {
                    $other_permissions[$value] = 1;
                } else {
                    $other_permissions[$value] = 0;
                }
            }
            $package_permissions = [
                'sms_settings_access' => !empty($request->access_sms_settings) ? $request->access_sms_settings : 0,
                'account_access' =>  !empty($request->access_account) ? $request->access_account : 0,
                'module_access' =>  !empty($request->access_module) ? $request->access_module : 0
            ];

            $manufacturing_module = ['manufacturing_module' =>  !empty($request->mf_module) ? 1 : '0'];
            $current_values = !empty($request->current_values) ? $request->current_values : [];
            $pacakge_data = array(
                'name' => $business->name,
                'description' => 'Custom Package',
                'package_permissions' => json_encode($package_permissions),
                'location_count' => !empty($request->location_count) ? $request->location_count : 1,
                'user_count' => !empty($already_running_pacakge->user_count) ? $already_running_pacakge->user_count : 1,
                'product_count' => !empty($request->product_count) ? $request->product_count : 1,
                'vehicle_count' => !empty($request->vehicle_count) ? $request->vehicle_count : 1,
                'visit_count' => !empty($already_running_pacakge->visit_count) ? $already_running_pacakge->visit_count : 1,
                'invoice_count' => !empty($already_running_pacakge->invoice_count) ? $already_running_pacakge->invoice_count : 1,
                'store_count' => !empty($already_running_pacakge->store_count) ? $already_running_pacakge->store_count : 1,
                'interval_count' => !empty($already_running_pacakge->interval_count) ? $already_running_pacakge->interval_count : 1,
                'customer_count' => !empty($already_running_pacakge->customer_count) ? $already_running_pacakge->customer_count : 1,
                'bookings' => !empty($request->enable_booking) ? $request->enable_booking : 0,
                'booking' => !empty($request->enable_booking) ? $request->enable_booking : 0,
                'interval' => 'years',
                'trial_days' => '0',
                'price' => number_format($annual_fee_package, 2, '.', ''),
                'created_by' => Auth::user()->id,
                'is_active' => '1',
                'sort_order' => '1',
                'hospital_business_type' => '[]',
                'sales_commission_agent' => !empty($request->enable_sale_cmsn_agent) ? $request->enable_sale_cmsn_agent : 0,
                'customer_order_own_customer' => !empty($request->customer_order_own_customer) ? $request->customer_order_own_customer : 0,
                'customer_settings' => !empty($request->customer_settings) ? $request->customer_settings : 0,
                'customer_order_general_customer' => !empty($request->customer_order_general_customer) ? $request->customer_order_general_customer : 0,
                'customer_to_directly_in_panel' => !empty($request->customer_to_directly_in_panel) ? $request->customer_to_directly_in_panel : 0,
                'restaurant' => !empty($request->enable_restaurant) ? $request->enable_restaurant : 0,
                'crm_enable' => !empty($request->enable_crm) ? $request->enable_crm : 0,
                'manufacturer' => !empty($request->mf_module) ? $request->mf_module : 0,
                'sms_enable' => !empty($request->enable_sms) ? $request->enable_sms : 0,
                'hr_module' => !empty($request->hr_module) ? $request->hr_module : 0,
                'employee' => !empty($request->employee) ? $request->employee : 0,
                'teminated' => !empty($request->teminated) ? $request->teminated : 0,
                'award' => !empty($request->award) ? $request->award : 0,
                'leave_request' => !empty($request->leave_request) ? $request->leave_request : 0,
                'attendance' => !empty($request->attendance) ? $request->attendance : 0,
                'import_attendance' => !empty($request->import_attendance) ? $request->import_attendance : 0,
                'late_and_over_time' => !empty($request->late_and_over_time) ? $request->late_and_over_time : 0,
                'payroll' => !empty($request->payroll) ? $request->payroll : 0,
                'salary_details' => !empty($request->salary_details) ? $request->salary_details : 0,
                'basic_salary' => !empty($request->basic_salary) ? $request->basic_salary : 0,
                'payroll_payments' => !empty($request->payroll_payments) ? $request->payroll_payments : 0,
                'hr_reports' => !empty($request->hr_reports) ? $request->hr_reports : 0,
                'attendance_report' => !empty($request->attendance_report) ? $request->attendance_report : 0,
                'employee_report' => !empty($request->employee_report) ? $request->employee_report : 0,
                'payroll_report' => !empty($request->payroll_report) ? $request->payroll_report : 0,
                'notice_board' => !empty($request->notice_board) ? $request->notice_board : 0,
                'hr_settings' => !empty($request->hr_settings) ? $request->hr_settings : 0,
                'department' => !empty($request->department) ? $request->department : 0,
                'jobtitle' => !empty($request->jobtitle) ? $request->jobtitle : 0,
                'jobcategory' => !empty($request->jobcategory) ? $request->jobcategory : 0,
                'workingdays' => !empty($request->workingdays) ? $request->workingdays : 0,
                'workshift' => !empty($request->workshift) ? $request->workshift : 0,
                'holidays' => !empty($request->holidays) ? $request->holidays : 0,
                'leave_type' => !empty($request->leave_type) ? $request->leave_type : 0,
                'salary_grade' => !empty($request->salary_grade) ? $request->salary_grade : 0,
                'employment_status' => !empty($request->employment_status) ? $request->employment_status : 0,
                'hr_prefix' => !empty($request->hr_prefix) ? $request->hr_prefix : 0,
                'hr_tax' => !empty($request->hr_tax) ? $request->hr_tax : 0,
                'religion' => !empty($request->religion) ? $request->religion : 0,
                'hr_setting_page' => !empty($request->hr_setting_page) ? $request->hr_setting_page : 0,
                'petro_module' => !empty($request->enable_petro_module) ? $request->enable_petro_module : 0,
                'meter_resetting' => !empty($request->meter_resetting) ? $request->meter_resetting : 0,
                'tasks_management' => !empty($request->tasks_management) ? $request->tasks_management : 0,
                'notes_page' => !empty($request->notes_page) ? $request->notes_page : 0,
                'tasks_page' => !empty($request->tasks_page) ? $request->tasks_page : 0,
                'reminder_page' => !empty($request->reminder_page) ? $request->reminder_page : 0,
                'member_registration' => !empty($request->member_registration) ? $request->member_registration : 0,
                'visitors_registration_module' => !empty($request->visitors_registration_module) ? $request->visitors_registration_module : 0,
                'visitors' => !empty($request->visitors) ? $request->visitors : 0,
                'visitors_registration' => !empty($request->visitors_registration) ? $request->visitors_registration : 0,
                'visitors_registration_setting' => !empty($request->visitors_registration_setting) ? $request->visitors_registration_setting : 0,
                'visitors_district' => !empty($request->visitors_district) ? $request->visitors_district : 0,
                'visitors_town' => !empty($request->visitors_town) ? $request->visitors_town : 0,
                'disable_all_other_module_vr' => !empty($request->disable_all_other_module_vr) ? $request->disable_all_other_module_vr : 0,
                'catalogue_qr' => !empty($request->catalogue_qr) ? $request->catalogue_qr : 0,
                'pay_excess_commission' => !empty($request->pay_excess_commission) ? $request->pay_excess_commission : 0,
                'recover_shortage' => !empty($request->recover_shortage) ? $request->recover_shortage : 0,
                'pump_operator_ledger' => !empty($request->pump_operator_ledger) ? $request->pump_operator_ledger : 0,
                'select_pump_operator_in_settlement' => !empty($request->select_pump_operator_in_settlement) ? $request->select_pump_operator_in_settlement : 0,
                'commission_type' => !empty($request->commission_type) ? $request->commission_type : 0,
                'mpcs_module' => !empty($request->mpcs_module) ? $request->mpcs_module : 0,
                'fleet_module' => !empty($request->fleet_module) ? $request->fleet_module : 0,
                'mpcs_form_settings' => !empty($request->mpcs_form_settings) ? $request->mpcs_form_settings : 0,
                'list_opening_values' => !empty($request->list_opening_values) ? $request->list_opening_values : 0,
                'merge_sub_category' => !empty($request->merge_sub_category) ? $request->merge_sub_category : 0,
                'backup_module' => !empty($request->backup_module) ? $request->backup_module : 0,
                'enable_separate_customer_statement_no' => !empty($request->enable_separate_customer_statement_no) ? $request->enable_separate_customer_statement_no : 0,
                'edit_customer_statement' => !empty($request->edit_customer_statement) ? $request->edit_customer_statement : 0,
                'enable_cheque_writing' => !empty($request->enable_cheque_writing) ? $request->enable_cheque_writing : 0,
                'home_dashboard' => !empty($request->home_dashboard) ? $request->home_dashboard : 0,
                'contact_module' => !empty($request->contact_module) ? $request->contact_module : 0,
                'property_module' => !empty($request->property_module) ? $request->property_module : 0,
                'tank_dip_chart' => !empty($request->tank_dip_chart) ? $request->tank_dip_chart : 0,
                'ran_module' => !empty($request->ran_module) ? $request->ran_module : 0,
                'report_module' => !empty($request->report_module) ? $request->report_module : 0,
                'verification_report' => !empty($request->verification_report) ? $request->verification_report : 0,
                'monthly_report' => !empty($request->monthly_report) ? $request->monthly_report : 0,
                'comparison_report' => !empty($request->comparison_report) ? $request->comparison_report : 0,
                'notification_template_module' => !empty($request->notification_template_module) ? $request->notification_template_module : 0,
                'list_easy_payment' => !empty($request->list_easy_payment) ? $request->list_easy_payment : 0,
                'settings_module' => !empty($request->settings_module) ? $request->settings_module : 0,
                'business_settings' => !empty($request->business_settings) ? $request->business_settings : 0,
                'business_location' => !empty($request->business_location) ? $request->business_location : 0,
                'invoice_settings' => !empty($request->invoice_settings) ? $request->invoice_settings : 0,
                'tax_rates' => !empty($request->tax_rates) ? $request->tax_rates : 0,
                'user_management_module' => !empty($request->user_management_module) ? $request->user_management_module : 0,
                'banking_module' => !empty($request->banking_module) ? $request->banking_module : 0,
                'orders' => !empty($request->orders) ? $request->orders : 0,
                'products' => !empty($request->products) ? $request->products : 0,
                'purchase' => !empty($request->purchase) ? $request->purchase : 0,
                'stock_transfer' => !empty($request->stock_transfer) ? $request->stock_transfer : 0,
                'service_staff' => !empty($request->service_staff) ? $request->service_staff : 0,
                'enable_subscription' => !empty($request->enable_subscription) ? $request->enable_subscription : 0,
                'stock_adjustment' => !empty($request->stock_adjustment) ? $request->stock_adjustment : 0,
                'tables' => !empty($request->tables) ? $request->tables : 0,
                'type_of_service' => !empty($request->type_of_service) ? $request->type_of_service : 0,
                'sale_module' => !empty($request->sale_module) ? $request->sale_module : 0,
                'all_sales' => !empty($request->all_sales) ? $request->all_sales : 0,
                'add_sale' => !empty($request->add_sale) ? $request->add_sale : 0,
                'list_pos' => !empty($request->list_pos) ? $request->list_pos : 0,
                'list_draft' => !empty($request->list_draft) ? $request->list_draft : 0,
                'list_quotation' => !empty($request->list_quotation) ? $request->list_quotation : 0,
                'list_sell_return' => !empty($request->list_sell_return) ? $request->list_sell_return : 0,
                'shipment' => !empty($request->shipment) ? $request->shipment : 0,
                'discount' => !empty($request->discount) ? $request->discount : 0,
                'import_sale' => !empty($request->import_sale) ? $request->import_sale : 0,
                'reserved_stock' => !empty($request->reserved_stock) ? $request->reserved_stock : 0,
                'pos_sale' => !empty($request->pos_sale) ? $request->pos_sale : 0,
                'expenses' => !empty($request->expenses) ? $request->expenses : 0,
                'modifiers' => !empty($request->modifiers) ? $request->modifiers : 0,
                'kitchen' => !empty($request->kitchen) ? $request->kitchen : 0,
                'upload_images' => !empty($request->upload_images) ? $request->upload_images : 0,
                'leads_module' => !empty($request->leads_module) ? $request->leads_module : 0,
                'leads' => !empty($request->leads) ? $request->leads : 0,
                'day_count' => !empty($request->day_count) ? $request->day_count : 0,
                'leads_import' => !empty($request->leads_import) ? $request->leads_import : 0,
                'leads_settings' => !empty($request->leads_settings) ? $request->leads_settings : 0,
                'sms_module' => !empty($request->sms_module) ? $request->sms_module : 0,
                'cache_clear' => !empty($request->cache_clear) ? $request->cache_clear : 0,
                'pump_operator_dashboard' => !empty($request->pump_operator_dashboard) ? $request->pump_operator_dashboard : 0,
                'list_sms' => !empty($request->list_sms) ? $request->list_sms : 0,
                'status_order' => !empty($request->status_order) ? $request->status_order : 0,
                'list_orders' => !empty($request->list_orders) ? $request->list_orders : 0,
                'upload_orders' => !empty($request->upload_orders) ? $request->upload_orders : 0,
                'subcriptions' => !empty($request->subcriptions) ? $request->subcriptions : 0,
                'over_limit_sales' => !empty($request->over_limit_sales) ? $request->over_limit_sales : 0,
                'repair_module' => !empty($request->repair_module) ? $request->repair_module : 0,
                'job_sheets' => !empty($request->job_sheets) ? $request->job_sheets : 0,
                'add_job_sheet' => !empty($request->add_job_sheet) ? $request->add_job_sheet : 0,
                'list_invoice' => !empty($request->list_invoice) ? $request->list_invoice : 0,
                'add_invoice' => !empty($request->add_invoice) ? $request->add_invoice : 0,
                'brands' => !empty($request->brands) ? $request->brands : 0,
                'repair_settings' => !empty($request->repair_settings) ? $request->repair_settings : 0,
                'monthly_total_sale_value' => !empty($request->monthly_total_sales_volumn) ? $request->monthly_total_sales_volumn : 0,
                'enable_duplicate_invoice' => !empty($request->enable_duplicate_invoice) ? $request->enable_duplicate_invoice : 0,
                'hospital_system' => !empty($request->hospital_system) ? $request->hospital_system : 0,
                'only_for_business' => $business->id,
                'number_of_branches' => $this->getVarialbeSelected(0, $request->opt_vars),
                'number_of_users' => $this->getVarialbeSelected(1, $request->opt_vars),
                'number_of_products' => $this->getVarialbeSelected(2, $request->opt_vars),
                'number_of_periods' => $this->getVarialbeSelected(3, $request->opt_vars),
                'number_of_customers' => $this->getVarialbeSelected(4, $request->opt_vars),
                'number_of_stores' => $this->getVarialbeSelected(5, $request->opt_vars),
                'module_enable_price' => json_encode($module_enable_price),
                'manage_module_enable' => json_encode($manage_module_enable),
                'other_permissions' => json_encode($other_permissions),
                'currency_id' => $request->currency_id,

                'customer_interest_deduct_option' =>
                    !empty($request->customer_interest_deduct_option) ?
                        $request->customer_interest_deduct_option : 0,

            );

            $pacakge_data['current_values'] = 0;
            if(System::getProperty('create_individual_company_package') == 'yes'){
                $pacakge_data['current_values'] = 1;
            }
            if (!empty($request->package_manage_id)) {
                $pacakge_data['custom_permissions'] = json_encode($manufacturing_module);
                $pacakge_data['current_values'] = json_encode($current_values);
                if (!empty($request->opt_vars)) {
                    $pacakge_data['option_variables'] = $request->opt_vars;
                }
                Package::where('id', $request->package_manage_id)->update($pacakge_data);
            } else {
                $pacakge_data['option_variables'] = $request->opt_vars;
                $pacakge_data['custom_permissions'] = $manufacturing_module;
                $pacakge_data['current_values'] = $current_values;
                $new_package =  Package::create($pacakge_data);
                // Subscription::where('id', $subscription->id)->update(['package_id' => $new_package->id]);
            }

            //upadting other permission for current company
            if (!empty($subscription)) {
                $subscription = Subscription::where('id', $subscription->id)->select('id', 'package_details')->first();

                $package_details = $subscription->package_details;
                foreach ($other_permissions_array as $value) {
                    $package_details[$value] = $other_permissions[$value];
                }
                //updating these new value to subscription
                $package_details['location_count'] = !empty($request->location_count) ? $request->location_count : 1;
                $package_details['product_count'] = !empty($request->product_count) ? $request->product_count : 1;
                $package_details['vehicle_count'] = !empty($request->vehicle_count) ? $request->vehicle_count : 1;

                Subscription::where('id', $subscription->id)->update(['package_details' => json_encode($package_details)]);
            }

            $business_data['background_showing_type'] = $request->background_showing_type;
            $business_data['customer_interest_deduct_option'] = !empty($request->customer_interest_deduct_option) ? $request->customer_interest_deduct_option : 0;
            //upload background image file
            if (!file_exists('./public/uploads/business_data/' . $id)) {
                mkdir('./public/uploads/business_data/' . $id, 0777, true);
            }
            if ($request->hasfile('background_image')) {
                $file = $request->file('background_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/uploads/business_data/' . $id, $filename);
                $uploadFileFicon = 'public/uploads/business_data/' . $id . '/' . $filename;
                $business_data['background_image'] = $uploadFileFicon;
            }
            //upload logo image file
            if (!file_exists('./public/uploads/business_data/' . $id)) {
                mkdir('./public/uploads/business_data/' . $id, 0777, true);
            }
            if ($request->hasfile('logo')) {
                $file = $request->file('logo');
                $extension = $file->getClientOriginalExtension();
                $logo_filename = time() . '.' . $extension;
                $file->move('public/uploads/business_logos/', $logo_filename);
                $business_data['logo'] = $logo_filename;
            }

            $business_data['day_end_enable'] = $request->day_end_enable == '1' ? 1 : 0;

            Business::where('id', $id)->update($business_data);


            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\BusinessController@index')
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();

            // return "Line:" . $e->getLine() . "Message:" . $e->getMessage();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return back()->with('status', $output)->withInput();
        }
    }

    public function getVarialbeSelected($option_id, $opt_vars)
    {
        if (!empty($opt_vars)) {
            $opt_vars = json_decode($opt_vars);
            if (!empty($opt_vars)) {
                foreach ($opt_vars as $opt) {
                    $op = CompanyPackageVariable::where('id', $opt)->first();
                    if ($op->variable_options == $option_id) {
                        return '1';
                    }
                }
            }
        }

        return '0';
    }

    public function deleteAllPreviousAccountTransactions($business_id)
    {
        $transaction_account = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
            ->where('transactions.business_id', $business_id)->where('account_transactions.deleted_at', null)->select('account_transactions.id')->get();
        $tansaction_payments = AccountTransaction::leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
            ->where('transaction_payments.business_id', $business_id)->where('account_transactions.deleted_at', null)->select('account_transactions.id')->get();
        foreach ($transaction_account as $at) {
            AccountTransaction::where('id', $at->id)->delete();
        }
        foreach ($tansaction_payments as $ap) {
            AccountTransaction::where('id', $ap->id)->delete();
        }
    }

    public function loginAsBusiness($id)
    {

        $business_util = new BusinessUtil;

        $user = User::where('business_id', $id)->first();
        Auth::loginUsingId($user->id);
        $session_data = [
            'id' => $user->id,
            'surname' => $user->surname,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'business_id' => $user->business_id,
            'language' => $user->language,
        ];
        $business = Business::findOrFail($user->business_id);

        $currency = $business->currency;
        $currency_data = [
            'id' => $currency->id,
            'code' => $currency->code,
            'symbol' => $currency->symbol,
            'thousand_separator' => $currency->thousand_separator,
            'decimal_separator' => $currency->decimal_separator
        ];
        request()->session()->forget('user');
        request()->session()->forget('business');
        request()->session()->forget('currency');
        request()->session()->forget('financial_year');
        request()->session()->put('user', $session_data);
        request()->session()->put('business', $business);
        request()->session()->put('currency', $currency_data);
        request()->session()->put('superadmin-logged-in', '1');

        //set current financial year to session
        $financial_year = $business_util->getCurrentFinancialYear($business->id);
        request()->session()->put('financial_year', $financial_year);

        return redirect('home');
    }
    public function backToSuperadmin()
    {

        $business_util = new BusinessUtil;

        $user = User::where('business_id', 1)->first();
        Auth::loginUsingId($user->id);
        $session_data = [
            'id' => $user->id,
            'surname' => $user->surname,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'business_id' => $user->business_id,
            'language' => $user->language,
        ];
        $business = Business::findOrFail($user->business_id);

        $currency = $business->currency;
        $currency_data = [
            'id' => $currency->id,
            'code' => $currency->code,
            'symbol' => $currency->symbol,
            'thousand_separator' => $currency->thousand_separator,
            'decimal_separator' => $currency->decimal_separator
        ];
        request()->session()->forget('user');
        request()->session()->forget('business');
        request()->session()->forget('currency');
        request()->session()->forget('financial_year');
        request()->session()->put('user', $session_data);
        request()->session()->put('business', $business);
        request()->session()->put('currency', $currency_data);
        request()->session()->forget('superadmin-logged-in');

        //set current financial year to session
        $financial_year = $business_util->getCurrentFinancialYear($business->id);
        request()->session()->put('financial_year', $financial_year);

        return redirect('home');
    }
}
