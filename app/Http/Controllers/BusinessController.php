<?php
namespace App\Http\Controllers;
use App\Business;
use App\Currency;
use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Agent;
use App\BusinessCategory;
use App\ContactGroup;
use App\DefaultAccount;
use App\DefaultAccountGroup;
use App\DefaultAccountType;
use App\Notifications\TestEmailNotification;
use App\Store;
use App\System;
use App\TaxRate;
use App\Unit;
use App\User;
use App\PatientDetail;
use App\Product;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Superadmin\Entities\HelpExplanation;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Spatie\Permission\Models\Permission;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Notifications\NewBusinessWelcomNotification;
use PDO;
use Symfony\Component\CssSelector\Node\FunctionNode;
use \Notification;
class BusinessController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BusinessController
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new business/business as well as their
    | validation and creation.
    |
    */
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $mailDrivers;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, RestaurantUtil $restaurantUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->theme_colors = [
            'blue' => 'Blue',
            'black' => 'Black',
            'purple' => 'Purple',
            'green' => 'Green',
            'red' => 'Red',
            'yellow' => 'Yellow',
            'blue-light' => 'Blue Light',
            'black-light' => 'Black Light',
            'purple-light' => 'Purple Light',
            'green-light' => 'Green Light',
            'red-light' => 'Red Light',
        ];
        $this->mailDrivers = [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'mandrill' => 'Mandrill',
            'ses' => 'SES',
            'sparkpost' => 'Sparkpost'
        ];
    }
    /**
     * Shows registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }
        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }
        $accounting_methods = $this->businessUtil->allAccountingMethods();
        $package_id = request()->package;
        $system_settings = System::getProperties(['superadmin_enable_register_tc', 'superadmin_register_tc'], true);
        return view('business.register', compact(
            'currencies',
            'timezone_list',
            'months',
            'accounting_methods',
            'package_id',
            'system_settings'
        ));
    }
    /**
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postAgentRegister(Request $request)
    {
        try {
            $agent_details = $request->except('_token', 'confirm_password', 'package_id');
            $agent_details['password'] = Hash::make($agent_details['password']);
            $agent_details['date'] = Carbon::now();
            $agent_details['nic_copy'] = $this->businessUtil->uploadFile($request, 'nic_copy', 'agents', 'image');
            $agent_details['agent_photo'] = $this->businessUtil->uploadFile($request, 'agent_photo', 'agents', 'image');
            DB::beginTransaction();
            $agent = Agent::create($agent_details);
            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_agent_created', ['agent' => $agent]);
            }
            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            $title = System::getProperty('agent_register_success_title');
            $msg = System::getProperty('agent_register_success_msg');
            $msg = str_replace('{username}', $agent->username, $msg);
            $msg = str_replace('{referral_code}', $agent->referral_code, $msg);
            $msg = str_replace('{name}', $agent->name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);
            DB::commit();
            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            return redirect('login')->with('register_success', $output);
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
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }
        try {
            $validator = $request->validate(
                [
                    'name' => 'required|max:255',
                    'currency_id' => 'required|numeric',
                    'country' => 'required|max:255',
                    'state' => 'required|max:255',
                    'city' => 'required|max:255',
                    'zip_code' => 'required|max:255',
                    'landmark' => 'required|max:255',
                    'time_zone' => 'required|max:255',
                    'surname' => 'max:10',
                    'email' => 'sometimes|nullable|email|unique:users|max:255',
                    'first_name' => 'required|max:255',
                    'username' => 'required|min:4|max:255|unique:users',
                    'password' => 'required|min:4|max:255',
                    'fy_start_month' => 'required',
                    'accounting_method' => 'required',
                ],
                [
                    'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
                    'name.currency_id' => __('validation.required', ['attribute' => __('business.currency')]),
                    'country.required' => __('validation.required', ['attribute' => __('business.country')]),
                    'state.required' => __('validation.required', ['attribute' => __('business.state')]),
                    'city.required' => __('validation.required', ['attribute' => __('business.city')]),
                    'zip_code.required' => __('validation.required', ['attribute' => __('business.zip_code')]),
                    'landmark.required' => __('validation.required', ['attribute' => __('business.landmark')]),
                    'time_zone.required' => __('validation.required', ['attribute' => __('business.time_zone')]),
                    'email.email' => __('validation.email', ['attribute' => __('business.email')]),
                    'email.email' => __('validation.unique', ['attribute' => __('business.email')]),
                    'first_name.required' => __('validation.required', ['attribute' =>
                    __('business.first_name')]),
                    'username.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'username.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'password.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'fy_start_month.required' => __('validation.required', ['attribute' => __('business.fy_start_month')]),
                    'accounting_method.required' => __('validation.required', ['attribute' => __('business.accounting_method')]),
                ]
            );
            DB::beginTransaction();
            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);
            $owner_details['language'] = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            $user = User::create_user($owner_details);
            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['fy_start_month'] = 1;
            $package_id_business = $request->get('package_id_business', null);
            if (!empty($package_id_business)) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id_business);
                if (!empty($package)) {
                    if (in_array('hosp_and_dis', json_decode($package->hospital_business_type))) {
                        $business_details['is_hospital'] = 1;
                        $startingHospitalPrefix = System::getProperty('hospital_prefix');
                        $business_details['name'] = $startingHospitalPrefix . '' . $business_details['name'];
                    }
                    if (in_array('pharmacies', json_decode($package->hospital_business_type))) {
                        $business_details['is_pharmacy'] = 1;
                        $startingPharmacyPrefix = System::getProperty('pharmacy_prefix');
                        $business_details['name'] = $startingPharmacyPrefix . '' . $business_details['name'];
                    }
                    if (in_array('laboratories', json_decode($package->hospital_business_type))) {
                        $business_details['is_laboratory'] = 1;
                        $startingLaboratoryPrefix = System::getProperty('laboratory_prefix');
                        $business_details['name'] = $startingLaboratoryPrefix . '' . $business_details['name'];
                    }
                }
            }
            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }
            $business_details['currency_precision'] = 2;
            $business_details['quantity_precision'] = 2;
            $business = $this->businessUtil->createNewBusiness($business_details);
            //Update user with business id
            $user->business_id = $business->id;
            $user->give_away_gifts = $request->give_away_gifts;
            $user->save();
            //create default accounts and account types
            $this->addAccounts($business->id);
            //create default Petro values
            $this->businessUtil->addPetroDefaults($business->id, $user->id);
            //create default MPCS settings
            $this->businessUtil->addMpcsDefaults($business->id);
            //create default Property Module Settings
            $this->businessUtil->addPropertyDefaults($business->id);
            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);
            //set defualt number of pumps for location
            ModulePermissionLocation::create(['business_id' => $business->id, 'module_name' => 'number_of_pumps', 'locations' => [$new_location->id => '12']]);
            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);
            //create default customer group for business
            $this->createDefaultContactGroup($business->id);
            $package_id = $request->get('package_id', null);
            $this->businessUtil->addReferral($request->referral_code, 'business', $business->id, $package_id);
            DB::commit();
            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }
            //Process payment information if superadmin is installed & package information is present
            $is_installed_superadmin = $this->moduleUtil->isSuperadminInstalled();
            $option_variables_selected = $request->get('option_variables_selected', null);
            $module_selected = $request->get('module_selected', null);
            $custom_price = $request->get('custom_price', null);
            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            $title = System::getProperty('company_register_success_title');
            $msg = System::getProperty('company_register_success_msg');
            $msg = str_replace('{business_name}', $business->name, $msg);
            $msg = str_replace('{username}', $user->username, $msg);
            $msg = str_replace('{first_name}', $user->first_name, $msg);
            $msg = str_replace('{last_name}', $user->last_name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);
            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id, 'option_variables_selected' => $option_variables_selected, 'module_selected' => $module_selected, 'custom_price' => $custom_price])->with('register_success', $output);
                }
            }
            return redirect('login')->with('register_success', $output);
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
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postVisitorRegister(Request $request)
    {
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }
        try {
            $validator = $request->validate(
                [
                    'name' => 'required|max:255',
                    'email' => 'sometimes|nullable|email|unique:users|max:255',
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'username' => 'required|min:4|max:255|unique:users',
                    'password' => 'required|min:4|max:255',
                    'country' => 'required|max:255',
                    'state' => 'required|max:255',
                    'city' => 'required|max:255',
                    'zip_code' => 'required|max:255',
                    'landmark' => 'required|max:255',
                ],
                [
                    'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
                    'email.email' => __('validation.unique', ['attribute' => __('business.email')]),
                    'first_name.required' => __('validation.required', ['attribute' => __('business.first_name')]),
                    'last_name.required' => __('validation.required', ['attribute' => __('business.last_name')]),
                    'username.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'username.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'password.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'country.required' => __('validation.required', ['attribute' => __('business.country')]),
                    'state.required' => __('validation.required', ['attribute' => __('business.state')]),
                    'city.required' => __('validation.required', ['attribute' => __('business.city')]),
                    'zip_code.required' => __('validation.required', ['attribute' => __('business.zip_code')]),
                    'landmark.required' => __('validation.required', ['attribute' => __('business.landmark')]),
                ]
            );
            DB::beginTransaction();
            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);
            $owner_details['language'] = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            $user = User::create_user($owner_details);
            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['fy_start_month'] = 1;
            $business_details['currency_id'] = 111;
            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }
            $business_details['currency_precision'] = 2;
            $business_details['quantity_precision'] = 2;
            $business = $this->businessUtil->createNewBusiness($business_details);
            //Update user with business id
            $user->business_id = $business->id;
            $user->give_away_gifts = $request->give_away_gifts;
            $user->save();
            //create default accounts and account types
            $this->addAccounts($business->id);
            //create default Petro values
            $this->businessUtil->addPetroDefaults($business->id, $user->id);
            //create default MPCS settings
            $this->businessUtil->addMpcsDefaults($business->id);
            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);
            //set defualt number of pumps for location
            ModulePermissionLocation::create(['business_id' => $business->id, 'module_name' => 'number_of_pumps', 'locations' => [$new_location->id => '12']]);
            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id]);
            //create default customer group for business
            $this->createDefaultContactGroup($business->id);
            $package_id = $request->get('package_id', null);
            $this->businessUtil->addReferral($request->referral_code, 'visitor', $business->id, $package_id);
            DB::commit();
            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }
            //Process payment information if superadmin is installed & package information is present
            $is_installed_superadmin = $this->moduleUtil->isSuperadminInstalled();
            $option_variables_selected = $request->get('option_variables_selected', null);
            $module_selected = $request->get('module_selected', null);
            $custom_price = $request->get('custom_price', null);
            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            $title = System::getProperty('visitor_register_success_title');
            $msg = System::getProperty('visitor_register_success_msg');
            $msg = str_replace('{username}', $user->username, $msg);
            $msg = str_replace('{first_name}', $user->first_name, $msg);
            $msg = str_replace('{last_name}', $user->last_name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);
            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id, 'option_variables_selected' => $option_variables_selected, 'module_selected' => $module_selected, 'custom_price' => $custom_price])->with('register_success', $output);
                }
            }
            return redirect('login')->with('register_success', $output);
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
    public function createDefaultContactGroup($business_id)
    {
        $customer_group = ContactGroup::where('business_id', $business_id)->where('type', 'customer')->where('name', 'Own Company')->first();
        if (empty($customer_group)) {
            ContactGroup::create([
                'business_id' => $business_id,
                'type' => 'customer',
                'name' => 'Own Company',
                'amount' => '0'
            ]);
        }
        $supplier_group = ContactGroup::where('business_id', $business_id)->where('type', 'supplier')->where('name', 'Own Company')->first();
        if (empty($supplier_group)) {
            ContactGroup::create([
                'business_id' => $business_id,
                'type' => 'supplier',
                'name' => 'Own Company',
                'amount' => '0'
            ]);
        }
        return true;
    }
    /**
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postPatientRegister(Request $request)
    {
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }
        try {
            DB::beginTransaction();
            $startingPatientPrefix = System::getProperty('patient_prefix');
            $startingPatientID = System::getProperty('patient_code_start_from');
            $last_pt_code = User::join('business', 'users.business_id', 'business.id')->orderBy('business.id', 'desc')->where('business.is_patient', '1')->first();
            if (!empty($last_pt_code)) {
                $nummber_of_c = strlen($startingPatientID);
                $next_user_id = str_replace('-', '', filter_var($last_pt_code->username, FILTER_SANITIZE_NUMBER_INT)) + 1;
                $next_user_code = $startingPatientPrefix . sprintf("%0" . $nummber_of_c . "d", $next_user_id);
            } else {
                $next_user_code = $startingPatientPrefix . '' . $startingPatientID;
            }
            //Create owner.
            $owner_details = $request->only(['p_email', 'p_password', 'language']);
            $owner_details['email'] = $owner_details['p_email'];
            $owner_details['password'] = $owner_details['p_password'];
            $owner_details['language'] = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            $owner_details['surname'] = '';
            $owner_details['first_name'] = $request->first_name;
            $owner_details['last_name'] = $request->last_name;
            $owner_details['username'] =  $next_user_code;
            $user = User::create_user($owner_details);
            $data_details = array(
                'user_id'  => $user->id,
                'name'  => $request->first_name,
                'address'  => $request->address,
                'country'  => $request->country,
                'state'  => $request->state,
                'city'  => $request->city,
                'mobile'  => $request->mobile,
                'date_of_birth'  => $request->date_of_birth,
                'gender'  => $request->gender,
                'marital_status'  => $request->marital_status,
                'blood_group'  => $request->blood_group,
                'height'  => $request->height,
                'weight'  => $request->weight,
                'guardian_name'  => $request->guardian_name,
                'time_zone'  => $request->time_zone,
                'known_allergies'  => $request->known_allergies,
                'notes'  => $request->notes
            );
            //upload prfile image
            if (!file_exists('./public/img/patient_photos')) {
                mkdir('./public/img/patient_photos', 0777, true);
            }
            if ($request->hasfile('fileToImage')) {
                $file = $request->file('fileToImage');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/patient_photos', $filename);
                $uploadFileFicon = 'public/img/patient_photos/' . $filename;
                $data_details['profile_image'] = $uploadFileFicon;
            } else {
                $data_details['profile_image'] = '';
            }
            $patient_details = PatientDetail::create($data_details);
            $business_details = $request->only(['name', 'time_zone']);
            $business_details['is_patient'] = 1;
            $business_details['patient_details_id'] = $patient_details->id;
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat(config('constants.default_date_format'), $request->join_date)->toDateString();
            }
            $business_details['currency_id'] = 1;
            $business = $this->businessUtil->createNewBusiness($business_details);
            //Update user with business id
            $user->business_id = $business->id;
            $user->give_away_gifts = $request->give_away_gifts;
            $user->save();
            $business_location = $request->only(['address', 'country', 'state', 'city', 'mobile']);
            $business_location['name'] = $business_location['address'];
            $business_location['zip_code'] = '';
            $business_location['landmark'] = '';
            $new_location = $this->businessUtil->addLocation($business->id, $business_location, 1, 1);
            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $package_id = $request->get('package_id', null);
            $this->businessUtil->addReferral($request->referral_code, 'patient', $business->id, $package_id);
            DB::commit();
            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }
            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            $title = System::getProperty('patient_register_success_title');
            $msg = System::getProperty('patient_register_success_msg');
            $msg = str_replace('{patient_code}', $user->username, $msg);
            $msg = str_replace('{first_name}', $user->first_name, $msg);
            $msg = str_replace('{last_name}', $user->last_name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);
            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            //Process payment information if superadmin is installed & package information is present
            $is_installed_superadmin = $this->moduleUtil->isSuperadminInstalled();
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id])->with('register_success', $output);
                }
            }
            return redirect('login')->with('register_success', $output);
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
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckUsername(Request $request)
    {
        $username = $request->input('username');
        if (!empty($request->input('username_ext'))) {
            $username .= $request->input('username_ext');
        }
        $count = User::where('username', $username)->count();
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }
    /**
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckUsernameAgent(Request $request)
    {
        $username = $request->input('username');
        if (!empty($request->input('username_ext'))) {
            $username .= $request->input('username_ext');
        }
        $count = Agent::where('username', $username)->count();
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }
    /**
     * Shows business settings form
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessSettings()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        //Check if subscribed or not, 
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $subscription = Subscription::active_subscription($business_id);
        $package_permission = json_encode(['sms_settings_access' => 0, 'account_access' => 0, 'module_access' => 0]);
        $enable_duplicate_invoice = 0;
        if (!empty($subscription)) {
            $package = DB::table('packages')->where('id', $subscription->package_id)->select('package_permissions', 'enable_duplicate_invoice')->first();
            $package_permissions = $package->package_permissions;
            $enable_duplicate_invoice = $package->enable_duplicate_invoice;
            if (!empty($package_permissions)) {
                $package_permissions = json_decode($package_permissions);
            } else {
                $package_permissions = json_decode($package_permission);
            }
        } else {
            $package_permissions = json_decode($package_permission);
        }
        $help_explanations = HelpExplanation::pluck('value', 'help_key');
        $package_details = $subscription->package_details;
        $is_superadmin = 0;
        if (auth()->user()->can('superadmin')) {
            $is_superadmin = 1;
        }
        $enabled_moudle_by_subscription = array( //module have permission in package subscribed
            'account' => $is_superadmin ? 1 : $package_details['access_account'],
            'banking_module' => $is_superadmin ? 1 : $package_details['banking_module'],
            'booking' => $is_superadmin ? 1 : $package_details['enable_booking'],
            'purchase' => $is_superadmin ? 1 : $package_details['purchase'],
            'stock_transfer' => $is_superadmin ? 1 : $package_details['stock_transfer'],
            'service_staff' => $package_details['service_staff'],
            'enable_subscription' => $is_superadmin ? 1 : $package_details['enable_subscription'],
            'add_sale' => $is_superadmin ? 1 : $package_details['add_sale'],
            'stock_adjustment' => $is_superadmin ? 1 : $package_details['stock_adjustment'],
            'tables' => $is_superadmin ? 1 : $package_details['tables'],
            'type_of_service' => $is_superadmin ? 1 : $package_details['type_of_service'],
            'pos_sale' => $is_superadmin ? 1 : $package_details['pos_sale'],
            'expenses' => $is_superadmin ? 1 : $package_details['expenses'],
            'modifiers' => $is_superadmin ? 1 : $package_details['modifiers'],
            'kitchen' => $is_superadmin ? 1 : $package_details['kitchen']
        );
        $currencies = $this->businessUtil->allCurrencies();
        $tax_details = TaxRate::forBusinessDropdown($business_id);
        $tax_rates = $tax_details['tax_rates'];
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }
        $accounting_methods = [
            'fifo' => __('business.fifo'),
            'lifo' => __('business.lifo')
        ];
        $commission_agent_dropdown = [
            '' => __('lang_v1.disable'),
            'logged_in_user' => __('lang_v1.logged_in_user'),
            'user' => __('lang_v1.select_from_users_list'),
            'cmsn_agnt' => __('lang_v1.select_from_commisssion_agents_list')
        ];
        $units_dropdown = Unit::forDropdown($business_id, true);
        $date_formats = Business::date_formats();
        $shortcuts = json_decode($business->keyboard_shortcuts, true);
        $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
        $email_settings = empty($business->email_settings) ? $this->businessUtil->defaultEmailSettings() : $business->email_settings;
        $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
        $modules = $this->moduleUtil->availableModules();
        $theme_colors = $this->theme_colors;
        $mail_drivers = $this->mailDrivers;
        $allow_superadmin_email_settings = System::getProperty('allow_email_settings_to_businesses');
        $custom_labels = !empty($business->custom_labels) ? json_decode($business->custom_labels, true) : [];
        $common_settings = !empty($business->common_settings) ? $business->common_settings : [];
		//dd($common_settings);
        $search_product_settings = !empty($business->search_product_settings) ? json_decode($business->search_product_settings) : [];
        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $business_categories = BusinessCategory::pluck('category_name', 'id');
        //create default customer group for business if not exist
        $this->createDefaultContactGroup($business_id);
        //create default mpcs settings for business if not exist
        $this->businessUtil->addMpcsDefaults($business_id);
        $get_permissions['restaurant'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_restaurant');
        $get_permissions['booking'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_booking');
        $get_permissions['access_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_module');
        $get_permissions['upload_images'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'upload_images');
        $get_permissions['cache_clear'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'cache_clear');
        $get_permissions['property_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');
        $get_permissions['fleet_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module');
        return view('business.settings', compact('help_explanations', 'get_permissions', 'enabled_moudle_by_subscription', 'business_categories', 'enable_duplicate_invoice', 'stores', 'search_product_settings', 'package_permissions', 'business', 'currencies', 'tax_rates', 'timezone_list', 'months', 'accounting_methods', 'commission_agent_dropdown', 'units_dropdown', 'date_formats', 'shortcuts', 'pos_settings', 'modules', 'theme_colors', 'email_settings', 'sms_settings', 'mail_drivers', 'allow_superadmin_email_settings', 'custom_labels', 'common_settings'));
    }
    /**
     * Updates business settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postBusinessSettings(Request $request)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->businessUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            $business_details = $request->only([
                'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'default_profit_percent', 'default_sales_tax', 'default_sales_discount', 'sell_price_tax', 'sku_prefix', 'time_zone', 'fy_start_month', 'accounting_method', 'transaction_edit_days', 'sales_cmsn_agnt', 'item_addition_method', 'currency_symbol_placement', 'on_product_expiry',
                'stop_selling_before', 'default_unit', 'expiry_type', 'date_format',
                'time_format', 'ref_no_prefixes', 'ref_no_starting_number', 'theme_color', 'email_settings',
                'sms_settings', 'rp_name', 'amount_for_unit_rp',
                'min_order_total_for_rp', 'max_rp_per_order',
                'redeem_amount_per_unit_rp', 'min_order_total_for_redeem',
                'min_redeem_point', 'max_redeem_point', 'rp_expiry_period',
                'rp_expiry_type', 'custom_labels', 'currency_precision', 'quantity_precision', 'reg_no', 'contact_fields','service_addition_method'
            ]);
            $business_details['business_categories'] = json_encode($request->business_categories);
            $business_details['show_for_customers'] = !empty($request->show_for_customers) ? $request->show_for_customers : 0;
            if (!empty($request->input('enable_rp')) &&  $request->input('enable_rp') == 1) {
                $business_details['enable_rp'] = 1;
            } else {
                $business_details['enable_rp'] = 0;
            }
            $business_details['amount_for_unit_rp'] = !empty($business_details['amount_for_unit_rp']) ? $this->businessUtil->num_uf($business_details['amount_for_unit_rp']) : 1;
            $business_details['min_order_total_for_rp'] = !empty($business_details['min_order_total_for_rp']) ? $this->businessUtil->num_uf($business_details['min_order_total_for_rp']) : 1;
            $business_details['redeem_amount_per_unit_rp'] = !empty($business_details['redeem_amount_per_unit_rp']) ? $this->businessUtil->num_uf($business_details['redeem_amount_per_unit_rp']) : 1;
            $business_details['min_order_total_for_redeem'] = !empty($business_details['min_order_total_for_redeem']) ? $this->businessUtil->num_uf($business_details['min_order_total_for_redeem']) : 1;
            $business_details['default_profit_percent'] = !empty($business_details['default_profit_percent']) ? $this->businessUtil->num_uf($business_details['default_profit_percent']) : 0;
            $business_details['default_sales_discount'] = !empty($business_details['default_sales_discount']) ? $this->businessUtil->num_uf($business_details['default_sales_discount']) : 0;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }
            if (!empty($request->day_end_enable)) {
                $business_details['day_end_enable'] = $request->day_end_enable;
            } else {
                $business_details['day_end_enable'] = 0;
            }
            if (!empty($request->input('enable_tooltip')) &&  $request->input('enable_tooltip') == 1) {
                $business_details['enable_tooltip'] = 1;
            } else {
                $business_details['enable_tooltip'] = 0;
            }
            $business_details['enable_product_expiry'] = !empty($request->input('enable_product_expiry')) &&  $request->input('enable_product_expiry') == 1 ? 1 : 0;
            if ($business_details['on_product_expiry'] == 'keep_selling') {
                $business_details['stop_selling_before'] = null;
            }
            $business_details['stock_expiry_alert_days'] = !empty($request->input('stock_expiry_alert_days')) ? $request->input('stock_expiry_alert_days') : 30;
            //Check for Purchase currency
            if (!empty($request->input('purchase_in_diff_currency')) &&  $request->input('purchase_in_diff_currency') == 1) {
                $business_details['purchase_in_diff_currency'] = 1;
                $business_details['purchase_currency_id'] = $request->input('purchase_currency_id');
                $business_details['p_exchange_rate'] = $request->input('p_exchange_rate');
            } else {
                $business_details['purchase_in_diff_currency'] = 0;
                $business_details['purchase_currency_id'] = null;
                $business_details['p_exchange_rate'] = 1;
            }
            $business_id = request()->session()->get('user.business_id');
            //upload images
            $business_details['background_showing_type'] = $request->background_showing_type;
            //upload login background image file
            if (!file_exists('./public/uploads/business_data/' . $business_id)) {
                mkdir('./public/uploads/business_data/' . $business_id, 0777, true);
            }
            if ($request->hasfile('background_image')) {
                $file = $request->file('background_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/uploads/business_data/' . $business_id, $filename);
                $uploadFileFicon = 'public/uploads/business_data/' . $business_id . '/' . $filename;
                $business_details['background_image'] = $uploadFileFicon;
            }
            //upload logo image file
            if (!file_exists('./public/uploads/business_data/' . $business_id)) {
                mkdir('./public/uploads/business_data/' . $business_id, 0777, true);
            }
            if ($request->hasfile('business_logo')) {
                $file = $request->file('business_logo');
                $extension = $file->getClientOriginalExtension();
                $logo_filename = time() . '.' . $extension;
                $file->move('public/uploads/business_logos/', $logo_filename);
                $business_details['logo'] = $logo_filename;
            }
            $checkboxes = [
                'enable_editing_product_from_purchase', 'enable_free_qty', 'popup_load_save_data',
                'enable_inline_tax',
                'enable_brand', 'enable_category', 'enable_sub_category', 'enable_price_tax', 'enable_purchase_status',
                'enable_lot_number', 'enable_racks', 'enable_row', 'enable_position', 'show_avai_qty_in_qr_catalogue', 'show_in_catalogue_page', 'enable_sub_units', 'enable_line_discount'
            ];
            if ($request->input('show_avai_qty_in_qr_catalogue') != $request->session()->get('business.show_avai_qty_in_qr_catalogue')) {
                Product::where('business_id', $business_id)->update(['show_avai_qty_in_qr_catalogue' => !empty($request->input('show_avai_qty_in_qr_catalogue')) ? 1 : 0]);
            }
            if ($request->input('show_in_catalogue_page') != $request->session()->get('business.show_in_catalogue_page')) {
                Product::where('business_id', $business_id)->update(['show_in_catalogue_page' => !empty($request->input('show_in_catalogue_page')) ? 1 : 0]);
            }
            foreach ($checkboxes as $value) {
                $business_details[$value] = !empty($request->input($value)) &&  $request->input($value) == 1 ? 1 : 0;
            }
            $business = Business::where('id', $business_id)->first();
            //Update business settings
            if (!empty($business_details['logo'])) {
                $business->logo = $business_details['logo'];
            } else {
                unset($business_details['logo']);
            }
            //System settings
            $shortcuts = $request->input('shortcuts');
            $business_details['keyboard_shortcuts'] = json_encode($shortcuts);
            //pos_settings
            $pos_settings = $request->input('pos_settings');
            $default_pos_settings = $this->businessUtil->defaultPosSettings();
            foreach ($default_pos_settings as $key => $value) {
                if (!isset($pos_settings[$key])) {
                    $pos_settings[$key] = $value;
                }
            }
            $pos_settings['enable_line_discount'] = $business_details['enable_line_discount'];
            $business_details['default_store'] = $request->input('default_store');
            $business_details['pos_settings'] = json_encode($pos_settings);
            $business_details['custom_labels'] = json_encode($business_details['custom_labels']);
            $business_details['search_product_settings'] = json_encode($request->search_product_settings);
            $business_details['common_settings'] = !empty($request->input('common_settings')) ? $request->input('common_settings') : [];
            //Enabled modules
            $enabled_modules = $request->input('enabled_modules');
            $business_details['enabled_modules'] = !empty($enabled_modules) ? $enabled_modules : null;
            $business->fill($business_details);
            $business->save();
            //update session data
            $request->session()->put('business', $business);
            //Update Currency details
            $currency = Currency::find($business->currency_id);
            $request->session()->put('currency', [
                'id' => $currency->id,
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
            ]);
            //update current financial year to session
            $financial_year = $this->businessUtil->getCurrentFinancialYear($business->id);
            $request->session()->put('financial_year', $financial_year);
            $output = [
                'success' => 1,
                'msg' => __('business.settings_updated_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect('business/settings')->with('status', $output);
    }
    /**
     * Handles the validation email
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckEmail(Request $request)
    {
        $email = $request->input('email');
        $query = User::where('email', $email);
        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            $query->where('id', '!=', $user_id);
        }
        $exists = $query->exists();
        if (!$exists) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }
    /**
     * Handles the validation email
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckEmailAgent(Request $request)
    {
        $email = $request->input('email');
        $query = Agent::where('email', $email);
        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            $query->where('id', '!=', $user_id);
        }
        $exists = $query->exists();
        if (!$exists) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }
    public function getEcomSettings()
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            $settings = Business::where('id', $api_settings->business_id)
                ->value('ecom_settings');
            $settings_array = !empty($settings) ? json_decode($settings, true) : [];
            if (!empty($settings_array['slides'])) {
                foreach ($settings_array['slides'] as $key => $value) {
                    $settings_array['slides'][$key]['image_url'] = !empty($value['image']) ? url('uploads/img/' . $value['image']) : '';
                }
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return $this->respondWentWrong($e);
        }
        return $this->respond($settings_array);
    }
    /**
     * Handles the testing of email configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmailConfiguration(Request $request)
    {
        try {
            $email_settings = $request->input();
            $data['email_settings'] = $email_settings;
            \Notification::route('mail', $email_settings['mail_from_address'])
                ->notify(new TestEmailNotification($data));
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.email_tested_successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }
        return $output;
    }
    /**
     * Handles the testing of sms configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function testSmsConfiguration(Request $request)
    {
        try {
            $sms_settings = $request->input();
            $data = [
                'sms_settings' => $sms_settings,
                'mobile_number' => $sms_settings['test_number'],
                'sms_body' => 'This is a test SMS'
            ];
            if (!empty($sms_settings['test_number'])) {
                $response = $this->businessUtil->sendSms($data);
            } else {
                $response = __('lang_v1.test_number_is_required');
            }
            $output = [
                'success' => 1,
                'msg' => $response
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }
        return $output;
    }
    public function dayEnd()
    {
        if (!auth()->user()->can('day_end.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $day_end = Business::where('id', $business_id)->select('day_end')->first()->day_end;
        Business::where('id', $business_id)->update(['day_end' => !$day_end]);
        return redirect()->back();
    }
    public function addAccounts($business_id)
    {
        $default_account_types = DefaultAccountType::orderBy('parent_account_type_id', 'asc')->get();
        foreach ($default_account_types as $key => $value) {
            $parent_accout_type = AccountType::where('business_id', $business_id)->where('default_account_type_id', $value->parent_account_type_id)->first();
            $type_array = array(
                'name' => $value->name,
                'business_id' => $business_id,
                'default_account_type_id' => $value->id,
                'parent_account_type_id' => !empty($parent_accout_type) ? $parent_accout_type->id : null
            );
            AccountType::create($type_array);
        }
        $default_account_groups = DefaultAccountGroup::all();
        foreach ($default_account_groups as $key => $value) {
            $account_type = AccountType::where('business_id', $business_id)->where('default_account_type_id', $value->account_type_id)->first();
            $account_group_array = array(
                'business_id' => $business_id,
                'name' => $value->name,
                'account_type_id' => !empty($account_type) ? $account_type->id : null,
                'note' => $value->note,
                'default_account_group_id' => $value->id
            );
            AccountGroup::create($account_group_array);
        }
        $default_accounts = DefaultAccount::all();
        foreach ($default_accounts as $key => $value) {
            $account_type = AccountType::where('business_id', $business_id)->where('default_account_type_id', $value->account_type_id)->first();
            $account_group = AccountGroup::where('business_id', $business_id)->where('default_account_group_id', $value->asset_type)->first();
            $account_array = array(
                'name' => $value->name,
                'business_id' => $business_id,
                'account_number' => $value->account_number,
                'account_type_id' => !empty($account_type) ? $account_type->id : null,
                'note' => $value->note,
                'asset_type' => !empty($account_group) ? $account_group->id : null,
                'created_by' => $value->created_by,
                'is_closed' => 0,
                'default_account_id' => $value->id
            );
            if (in_array($value->name, ['Accounts Receivable', 'Accounts Payable', 'Cards (Credit Debit) Account', 'Cash', 'Cheques in Hand', 'Customer Deposits', 'Petty Cash'])) {
                $account_array['visible'] = 1;
            } else {
                $account_array['visible'] = 0;
            }
            Account::create($account_array);
        }
        return true;
    }
    public function getBusinessByCategory(Request $request)
    {
        $html = '';
        $category = $request->category;
        $country = $request->country;
        if (empty($request->city)) {
            $cities = Business::whereJsonContains('business_categories', $category)->where('show_for_customers', 1)
                ->leftjoin('business_locations', 'business.id', 'business_locations.business_id')
                ->where('country', 'like', '%' . $country . '%')
                ->select('city')->groupBy('city')->get();
            if ($cities->count() != 0) {
                $html .= '<option value="" >Please Select</option>';
                foreach ($cities as $cities) {
                    $html .= '<option value ="' . $cities->city . '" >' . $cities->city . '</option>';
                }
                return ['type' => 'cities', 'html' => $html];
            }
        }
        $category = $request->category;
        $city = $request->city;
        $businesses = Business::whereJsonContains('business_categories', $category)->where('show_for_customers', 1)
            ->leftjoin('business_locations', 'business.id', 'business_locations.business_id')
            ->where('country', 'like', '%' . $country . '%')
            ->where('city', 'like', '%' . $city . '%')
            ->select('business.name', 'business.id')->get();
        if ($businesses->count() != 0) {
            foreach ($businesses as $business) {
                $html .= '<option value ="' . $business->id . '" >' . $business->name . '</option>';
            }
        }
        return ['type' => 'businesses', 'html' => $html];
    }
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
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
