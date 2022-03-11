<?php

namespace App\Utils;

use App\Account;
use App\Barcode;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\Currency;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\NotificationTemplate;
use App\Printer;
use App\Store;
use App\System;
use App\Transaction;
use App\Unit;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\LeaveApplicationType;
use Modules\MPCS\Entities\MpcsFormSetting;
use Modules\Property\Entities\BlockCloseReason;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Modules\Superadmin\Entities\Referral;
use Modules\TasksManagement\Entities\Priority;
use Spatie\Permission\Models\Role;

class BusinessUtil extends Util
{

    /**
     * Adds a default settings/resources for a new business
     *
     * @param int $business_id
     * @param int $user_id
     *
     * @return boolean
     */
    public function newBusinessDefaultResources($business_id, $user_id)
    {
        $user = User::find($user_id);

        //create Admin role and assign to user
        $role = Role::create([
            'name' => 'Admin#' . $business_id,
            'business_id' => $business_id,
            'guard_name' => 'web', 'is_default' => 1
        ]);
        $user->assignRole($role->name);

        //Create Cashier role for a new business
        $cashier_role = Role::create([
            'name' => 'Cashier#' . $business_id,
            'business_id' => $business_id,
            'guard_name' => 'web'
        ]);
        $cashier_role->syncPermissions(['sell.view', 'sell.create', 'sell.update', 'sell.delete', 'access_all_locations']);

        //Create Pump Operator role for a new business
        $pump_operator_role = Role::create([
            'name' => 'Pump Operator#' . $business_id,
            'business_id' => $business_id,
            'guard_name' => 'web'
        ]);
        $pump_operator_role->syncPermissions(['pump_operator.dashboard', 'access_all_locations']);

        $business = Business::findOrFail($business_id);

        //Update reference count
        $ref_count = $this->setAndGetReferenceCount('contacts', $business_id);
        $contact_id = $this->generateReferenceNumber('contacts', $ref_count, $business_id);

        //Add Default/Walk-In Customer for new business
        $customer = [
            'business_id' => $business_id,
            'type' => 'customer',
            'name' => 'Walk-In Customer',
            'created_by' => $user_id,
            'is_default' => 1,
            'contact_id' => $contact_id . '-' . $business_id
        ];
        Contact::create($customer);

        //create default invoice setting for new business
        InvoiceScheme::create([
            'name' => 'Default',
            'scheme_type' => 'blank',
            'prefix' => '',
            'start_number' => 1,
            'total_digits' => 4,
            'is_default' => 1,
            'business_id' => $business_id
        ]);
        //create default invoice layour for new business
        InvoiceLayout::create([
            'name' => 'Default',
            'header_text' => null,
            'invoice_no_prefix' => 'Invoice No.',
            'invoice_heading' => 'Invoice',
            'sub_total_label' => 'Subtotal',
            'discount_label' => 'Discount',
            'tax_label' => 'Tax',
            'total_label' => 'Total',
            'show_landmark' => 1,
            'show_city' => 1,
            'show_state' => 1,
            'show_zip_code' => 1,
            'show_country' => 1,
            'highlight_color' => '#000000',
            'footer_text' => '',
            'is_default' => 1,
            'business_id' => $business_id,
            'invoice_heading_not_paid' => '',
            'invoice_heading_paid' => '',
            'total_due_label' => 'Total Due',
            'paid_label' => 'Total Paid',
            'show_payments' => 1,
            'show_customer' => 1,
            'customer_label' => 'Customer',
            'table_product_label' => 'Product',
            'table_qty_label' => 'Quantity',
            'table_unit_price_label' => 'Unit Price',
            'table_subtotal_label' => 'Subtotal',
            'date_label' => 'Date'
        ]);

        //create default leave type for new business
        LeaveApplicationType::create([
            'business_id' => $business_id,
            'leave_type' => 'Half Day',
            'allowed_days' => null
        ]);
        LeaveApplicationType::create([
            'business_id' => $business_id,
            'leave_type' => 'Short Leave',
            'allowed_days' => null
        ]);

        //Add Default Unit for new business
        $unit = [
            'business_id' => $business_id,
            'actual_name' => 'Pieces',
            'short_name' => 'Pc(s)',
            'allow_decimal' => 0,
            'created_by' => $user_id
        ];
        Unit::create($unit);


        //Add default priority for comapany
        $priorities = [
            'Low',
            'Medium',
            'High',
            'Urgent',
            'Critical'
        ];

        foreach ($priorities as $priority) {
            $pr = [
                'business_id' => $business_id,
                'date' => date('Y-m-d'),
                'name' => $priority,
                'added_by' => 1
            ];
            Priority::create($pr);
        }

        //Create default notification templates
        $notification_templates = NotificationTemplate::defaultNotificationTemplates($business_id);
        foreach ($notification_templates as $notification_template) {
            NotificationTemplate::create($notification_template);
        }

        return true;
    }

    /**
     * Gives a list of all currencies
     *
     * @return array
     */
    public function allCurrencies()
    {
        $currencies = Currency::select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country')
            ->pluck('info', 'id');

        return $currencies;
    }

    /**
     * Gives a list of all timezone
     *
     * @return array
     */
    public function allTimeZones()
    {
        $datetime = new \DateTimeZone("EDT");

        $timezones = $datetime->listIdentifiers();
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }

        return $timezone_list;
    }

    /**
     * Gives a list of all accouting methods
     *
     * @return array
     */
    public function allAccountingMethods()
    {
        return [
            'fifo' => __('business.fifo'),
            'lifo' => __('business.lifo')
        ];
    }

    /**
     * Creates new business with default settings.
     *
     * @return object
     */
    public function createNewBusiness($business_details)
    {
        $business_details['sell_price_tax'] = 'includes';
        $business_details['currency_precision'] = '2';
        $business_details['quantity_precision'] = '2';

        $business_details['default_profit_percent'] = 25;

        //Add POS shortcuts
        $business_details['keyboard_shortcuts'] = '{"pos":{"express_checkout":"shift+e","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","recent_product_quantity":"f2","add_new_product":"f4"}}';

        //enable all module by default
        $business_details['enabled_modules'] = [
            "purchase",
            "add_sale",
            "pos_sale",
            "stock_transfer",
            "stock_adjustment",
            "expenses",
            "account",
            "banking_module",
            "modifiers",
            "subscription",
            "type_of_service",
            "service_staff",
        ];

        //Add prefixes
        $business_details['ref_no_prefixes'] = [
            'purchase' => 'PO',
            'stock_transfer' => 'ST',
            'stock_adjustment' => 'SA',
            'sell_return' => 'CN',
            'expense' => 'EP',
            'contacts' => 'CO',
            'purchase_payment' => 'PP',
            'sell_payment' => 'SP',
            'business_location' => 'BL',
            'customer' => 'CU',
            'supplier' => 'SU',
            'settlement' => 'ST',
        ];
        $business_details['ref_no_starting_number'] = [
            'purchase' => '1',
            'stock_transfer' => '1',
            'stock_adjustment' => '1',
            'sell_return' => '1',
            'expense' => '1',
            'contacts' => '1',
            'purchase_payment' => '1',
            'sell_payment' => '1',
            'business_location' => '1',
            'customer' => '1',
            'supplier' => '1',
            'settlement' => '1',
        ];
        $company_number_prefix = System::getProperty('company_number_prefix');
        $company_starting_number = (int) System::getProperty('company_starting_number');
        $count = Business::count();
        $number = $count +  $company_starting_number;

        $business_details['company_number'] = $company_number_prefix . $number;
        //Disable inline tax editing
        $business_details['enable_inline_tax'] = 0;

        $business = Business::create_business($business_details);

        return $business;
    }

    /**
     * Gives details for a business
     *
     * @return object
     */
    public function getDetails($business_id)
    {
        $details = Business::leftjoin('tax_rates AS TR', 'business.default_sales_tax', 'TR.id')
            ->leftjoin('currencies AS cur', 'business.currency_id', 'cur.id')
            ->select(
                'business.*',
                'cur.code as currency_code',
                'cur.symbol as currency_symbol',
                'thousand_separator',
                'decimal_separator',
                'TR.amount AS tax_calculation_amount',
                'business.default_sales_discount'
            )
            ->where('business.id', $business_id)
            ->first();

        return $details;
    }

    /**
     * Gives current financial year
     *
     * @return array
     */
    public function getCurrentFinancialYear($business_id)
    {
        $business = Business::where('id', $business_id)->first();
        $start_month = $business->fy_start_month;
        $end_month = $start_month - 1;
        if ($start_month == 1) {
            $end_month = 12;
        }

        $start_year = date('Y');
        //if current month is less than start month change start year to last year
        if (date('n') < $start_month) {
            $start_year = $start_year - 1;
        }

        $end_year = date('Y');
        //if current month is greater than end month change end year to next year
        if (date('n') > $end_month) {
            $end_year = $start_year + 1;
        }
        $start_date = $start_year . '-' . str_pad($start_month, 2, 0, STR_PAD_LEFT) . '-01';
        $end_date = $end_year . '-' . str_pad($end_month, 2, 0, STR_PAD_LEFT) . '-01';
        $end_date = date('Y-m-t', strtotime($end_date));

        $output = [
            'start' => $start_date,
            'end' =>  $end_date
        ];
        return $output;
    }

    /**
     * Adds a new location to a business
     *
     * @param int $business_id
     * @param array $location_details
     * @param int $invoice_layout_id default null
     *
     * @return location object
     */
    public function addLocation($business_id, $location_details, $invoice_scheme_id = null, $invoice_layout_id = null)
    {
        if (empty($invoice_scheme_id)) {
            $layout = InvoiceLayout::where('is_default', 1)
                ->where('business_id', $business_id)
                ->first();
            $invoice_layout_id = $layout->id;
        }

        if (empty($invoice_scheme_id)) {
            $scheme = InvoiceScheme::where('is_default', 1)
                ->where('business_id', $business_id)
                ->first();
            $invoice_scheme_id = $scheme->id;
        }

        //Update reference count
        $ref_count = $this->setAndGetReferenceCount('business_location', $business_id);
        $location_id = $this->generateReferenceNumber('business_location', $ref_count, $business_id);

        //Enable all payment methods by default
        $default_payment_accounts = json_decode(System::getProperty('default_payment_accounts'));

        $payment_types = $this->payment_types();
        $location_payment_types = [];

        foreach ($payment_types as $key => $value) {
            $is_enabled = 0;
            $account_id = null;
            if (!empty($default_payment_accounts->$key)) {
                $default_account_id = $default_payment_accounts->$key->account;

                $account = Account::where('business_id', $business_id)->where('default_account_id', $default_account_id)->first();

                if (!empty($account)) {
                    $account_id = $account->id;
                } else {
                    $account_id = null;
                }

                if (!empty($default_payment_accounts->$key->is_enabled)) {
                    $is_enabled = $default_payment_accounts->$key->is_enabled;
                } else {
                    $is_enabled = 0;
                }
            }

            $location_payment_types[$key] = [
                'is_enabled' => $is_enabled,
                'account' =>  $account_id
            ];
        }

        $location = BusinessLocation::create([
            'business_id' => $business_id,
            'name' => $location_details['name'],
            'landmark' => $location_details['landmark'],
            'city' => $location_details['city'],
            'state' => $location_details['state'],
            'zip_code' => $location_details['zip_code'],
            'country' => $location_details['country'],
            'invoice_scheme_id' => $invoice_scheme_id,
            'invoice_layout_id' => $invoice_layout_id,
            'mobile' => !empty($location_details['mobile']) ? $location_details['mobile'] : '',
            'alternate_number' => !empty($location_details['alternate_number']) ? $location_details['alternate_number'] : '',
            'website' => !empty($location_details['website']) ? $location_details['website'] : '',
            'email' => '',
            'location_id' => $location_id,
            'default_payment_accounts' => json_encode($location_payment_types)
        ]);
        return $location;
    }

    /**
     * Return the invoice layout details
     *
     * @param int $business_id
     * @param array $location_details
     * @param array $layout_id = null
     *
     * @return location object
     */
    public function invoiceLayout($business_id, $location_id, $layout_id = null)
    {
        $layout = null;
        if (!empty($layout_id)) {
            $layout = InvoiceLayout::find($layout_id);
        }

        //If layout is not found (deleted) then get the default layout for the business
        if (empty($layout)) {
            $layout = InvoiceLayout::where('business_id', $business_id)
                ->where('is_default', 1)
                ->first();
        }
        //$output = []
        return $layout;
    }

    /**
     * Return the printer configuration
     *
     * @param int $business_id
     * @param int $printer_id
     *
     * @return array
     */
    public function printerConfig($business_id, $printer_id)
    {
        $printer = Printer::where('business_id', $business_id)
            ->find($printer_id);

        $output = [];

        if (!empty($printer)) {
            $output['connection_type'] = $printer->connection_type;
            $output['capability_profile'] = $printer->capability_profile;
            $output['char_per_line'] = $printer->char_per_line;
            $output['ip_address'] = $printer->ip_address;
            $output['port'] = $printer->port;
            $output['path'] = $printer->path;
            $output['server_url'] = $printer->server_url;
        }

        return $output;
    }

    /**
     * Return the date range for which editing of transaction for a business is allowed.
     *
     * @param int $business_id
     * @param char $edit_transaction_period
     *
     * @return array
     */
    public function editTransactionDateRange($business_id, $edit_transaction_period)
    {
        if (is_numeric($edit_transaction_period)) {
            return [
                'start' => \Carbon::today()
                    ->subDays($edit_transaction_period),
                'end' => \Carbon::today()
            ];
        } elseif ($edit_transaction_period == 'fy') {
            //Editing allowed for current financial year
            return $this->getCurrentFinancialYear($business_id);
        }

        return false;
    }

    /**
     * Return the default setting for the pos screen.
     *
     * @return array
     */
    public function defaultPosSettings()
    {
        return ['duplicate_invoice_prefix' => 0, 'enable_prefix_duplicate_invoice' => 0, 'price_later' => 0, 'duplicate_invoice_prefix' => 0, 'disable_duplicate_invoice' => 0, 'disable_pay_checkout' => 0, 'disable_draft' => 0, 'disable_express_checkout' => 0, 'hide_product_suggestion' => 0, 'hide_recent_trans' => 0, 'disable_discount' => 0, 'disable_order_tax' => 0, 'is_pos_subtotal_editable' => 0];
    }

    /**
     * Return the default setting for the email.
     *
     * @return array
     */
    public function defaultEmailSettings()
    {
        return ['mail_host' => '', 'mail_port' => '', 'mail_username' => '', 'mail_password' => '', 'mail_encryption' => '', 'mail_from_address' => '', 'mail_from_name' => ''];
    }

    /**
     * Return form number 
     *
     * @return array
     */
    public function getFormNumber($type)
    {
        $business_id = request()->session()->get('business.id');
        $count = Transaction::where('business_id', $business_id)->where('type', $type)->count();
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_numbers = request()->session()->get('business.ref_no_starting_number');
        if ($type == 'property_purchase') {
            $type = 'purchase';
        }
        $prefix =   !empty($ref_no_prefixes[$type]) ? $ref_no_prefixes[$type] : '';
        $starting_no =   !empty($ref_no_starting_numbers[$type]) ? $ref_no_starting_numbers[$type] : 1;

        $number = 1;
        if (!empty($starting_no)) {
            $number = (int) $starting_no + $count;
        }

        $form_no = $prefix . $number;

        return $form_no;
    }

    /**
     * Return the default setting for the email.
     *
     * @return array
     */
    public function defaultSmsSettings()
    {
        return ['url' => '', 'send_to_param_name' => 'to', 'msg_param_name' => 'text', 'request_method' => 'post', 'param_1' => '', 'param_val_1' => '', 'param_2' => '', 'param_val_2' => '', 'param_3' => '', 'param_val_3' => '', 'param_4' => '', 'param_val_4' => '', 'param_5' => '', 'param_val_5' => '',];
    }

    /**
     * check contact id exist as username in users
     *
     * @return string
     */

    public function check_customer_code($business_id, $increment =  false)
    {
        $ref_count = $this->onlyGetReferenceCount('contacts', null, $increment);
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   $ref_no_prefixes['contacts'];
        $starting_number =   $ref_no_starting_number['contacts'];
        $contact_id = '';
        $next_number = $starting_number + $ref_count;
        $next_number =  str_pad($next_number, 4, 0, STR_PAD_LEFT);
        $contact_id =  $prefix . '-' . $next_number . '-' . $business_id;

        $check_exist = Contact::where('business_id', $business_id)->where('contact_id', $contact_id)->first();
        if (!empty($check_exist)) {
            return $this->check_customer_code($business_id, true);
        }
        return $contact_id;
    }


    /**
     * check contact id exist as username in users
     *
     * @return string
     */

    public function getIdWithIncrement($business_id, $for)
    {
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   $ref_no_prefixes[$for];
        $starting_number =   $ref_no_starting_number[$for];
        if (empty($starting_number)) {
            $starting_number = 1;
        }
        $count = 0;
        if ($for == 'business_location') {
            $count = BusinessLocation::where('business_id', $business_id)->count();
        }

        $next_number = (int) $starting_number + $count;

        $id = $prefix . $next_number;

        return $id;
    }


    /**
     * add petro fuel category for business
     *
     * @return string
     */

    public function addPetroDefaults($business_id, $user_id)
    {
        $cat_exist = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        if (empty($cat_exist)) {
            $category = array(
                'name' => 'Fuel',
                'business_id' => $business_id,
                'parent_id' => 0,
                'created_by' => $user_id
            );
            Category::create($category);
        }
    }
    /**
     * add MPCS default settings for business
     *
     * @return string
     */

    public function addMpcsDefaults($business_id)
    {
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        if (empty($settings)) {
            $data = array(
                'business_id' => $business_id,
                'F9C_sn' => 1,
                'F159ABC_form_sn' => 1,
                'F16A_form_sn' => 1,
                'F21C_form_sn' => 1,
                'F14_form_sn' => 1,
                'F17_form_sn' => 1,
                'F20_form_sn' => 1,
                'F21_form_sn' => 1,
                'F22_form_sn' => 1
            );
            MpcsFormSetting::create($data);
        }

        return true;
    }
    /**
     * add MPCS default settings for business
     *
     * @return string
     */

    public function addPropertyDefaults($business_id)
    {
        $data = array(
            'business_id' => $business_id,
            'date' => Carbon::now()->format('Y-m-d'),
            'reason' => 'Block Payments Completed',
            'created_by' => 1,
        );
        BlockCloseReason::create($data);


        return true;
    }

    /**
     * add referral code to system
     *
     * @return string
     */

    public function addReferral($referral_code, $model_type, $resource_id, $package_id)
    {
        $data = [
            'referral_code' => !empty($referral_code) ? $referral_code : '0',
            'model_type' => $model_type,
            'resource_id' => $resource_id,
            'package_id' => $package_id,
        ];

        Referral::create($data);
        return true;
    }


    /**
     * return import and export types
     *
     * @return string
     */

    public function getImportExportType()
    {
        return ['account_list' => __('superadmin::lang.account_list'), 'account_types' => __('superadmin::lang.account_types'), 'account_groups' => __('superadmin::lang.account_groups')];
    }
}
