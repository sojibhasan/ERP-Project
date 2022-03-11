<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

class Subscription extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'package_details' => 'array'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Scope a query to only include approved subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }

    /**
    * Get the package that belongs to the subscription.
    */
    public function package()
    {
        return $this->belongsTo('\Modules\Superadmin\Entities\Package')
            ->withTrashed();
    }

    /**
     * Returns the active subscription details for a business
     *
     * @param $business_id int
     *
     * @return Response
     */
    public static function active_subscription($business_id)
    {
        $date_today = \Carbon::today()->toDateString();

        $subscription = Subscription::where('business_id', $business_id)
                            ->whereDate('start_date', '<=', $date_today)
                            ->whereDate('end_date', '>=', $date_today)
                            ->approved()
                            ->first();

        return $subscription;
    }

    /**
     * Returns the upcoming subscription details for a business
     *
     * @param $business_id int
     *
     * @return Response
     */
    public static function upcoming_subscriptions($business_id)
    {
        $date_today = \Carbon::today();

        $subscription = Subscription::where('business_id', $business_id)
                            ->whereDate('start_date', '>', $date_today)
                            ->approved()
                            ->get();

        return $subscription;
    }

    /**
     * Returns the subscriptions waiting for approval for superadmin
     *
     * @param $business_id int
     *
     * @return Response
     */
    public static function waiting_approval($business_id)
    {
        $subscriptions = Subscription::where('business_id', $business_id)
                            ->whereNull('start_date')
                            ->waiting()
                            ->get();

        return $subscriptions;
    }

    public static function end_date($business_id)
    {
        $date_today = \Carbon::today();

        $subscription = Subscription::where('business_id', $business_id)
                            ->approved()
                            ->select(DB::raw("MAX(end_date) as end_date"))
                            ->first();
        if (empty($subscription->end_date)) {
            return $date_today;
        } else {
            return $subscription->end_date->addDay();
        }
    }
    public static function remaning_days($business_id)
    {
        $date_today = \Carbon::today();

        $end_date = Subscription::end_date($business_id);

        $diff = $end_date->diffInDays($date_today);

        return $diff;
    }

    /**
     * Returns the list of packages status
     *
     * @return array
     */
    public static function package_subscription_status()
    {
        return ['approved' => trans("superadmin::lang.approved"), 'declined' => trans("superadmin::lang.declined"), 'waiting' => trans("superadmin::lang.waiting")];
    }

    /**
     * Get the created_by.
     */
    public function created_user()
    {
        return $this->belongsTo(\App\User::class, 'created_id');
    }

    /**
     * Get the subscription business relationship.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Get the business permission array.
     */
    protected static function getBusinessPermissionsArray()
    {
        return [
            'mf_module',
            'access_account',
            'access_sms_settings',
            'access_module',
            'hospital_system',
            'enable_restaurant',
            'enable_booking',
            'enable_crm',
            'hr_module',
            'enable_duplicate_invoice',
            'enable_sms',
            'enable_sale_cmsn_agent',
            'monthly_total_sales_volumn',
            'customer_order_own_customer',
            'customer_settings',
            'customer_order_general_customer',
            'customer_to_directly_in_panel',
            'meter_resetting',
            'tasks_management',
            'notes_page',
            'tasks_page',
            'reminder_page',
            'member_registration',
            'visitors_registration_module',
            'visitors',
            'visitors_registration',
            'visitors_registration_setting',
            'visitors_district',
            'visitors_town',
            'disable_all_other_module_vr',
            'catalogue_qr',
            'pay_excess_commission',
            'recover_shortage',
            'pump_operator_ledger',
            'commission_type',
            'mpcs_module',
            'fleet_module',
            'ezyboat_module',
            'mpcs_form_settings',
            'list_opening_values',
            'enable_petro_module',
            'merge_sub_category',
            'backup_module',
            'enable_separate_customer_statement_no',
            'edit_customer_statement',
            'enable_cheque_writing',
            'issue_customer_bill',
            'home_dashboard',
            'contact_module',
            'contact_supplier',
            'contact_customer',
            'property_module',
            'tank_dip_chart',
            'ran_module',
            'report_module',
            'verification_report',
            'monthly_report',
            'comparison_report',
            'notification_template_module',
            'list_easy_payment',
            'settings_module',
            'business_settings',
            'business_location',
            'invoice_settings',
            'tax_rates',
            'user_management_module',
            'banking_module',
            'orders',
            'products',
            'purchase',
            'stock_transfer',
            'service_staff',
            'enable_subscription',
            'pos_sale',
            'status_order',
            'sale_module',
            'all_sales',
            'add_sale',
            'list_pos',
            'list_draft',
            'list_quotation',
            'list_sell_return',
            'shipment',
            'discount',
            'import_sale',
            'reserved_stock',
            'list_orders',
            'upload_orders',
            'subcriptions',
            'over_limit_sales',
            'stock_adjustment',
            'tables',
            'type_of_service',
            'expenses',
            'modifiers',
            'kitchen',
            'upload_images',
            'leads_module',
            'leads',
            'day_count',
            'leads_import',
            'leads_settings',
            'sms_module',
            'cache_clear',
            'pump_operator_dashboard',
            'list_sms',
            'employee',
            'teminated',
            'award',
            'leave_request',
            'attendance',
            'import_attendance',
            'late_and_over_time',
            'payroll',
            'salary_details',
            'basic_salary',
            'payroll_payments',
            'hr_reports',
            'attendance_report',
            'employee_report',
            'payroll_report',
            'notice_board',
            'hr_settings',
            'department',
            'jobtitle',
            'jobcategory',
            'workingdays',
            'workshift',
            'holidays',
            'leave_type',
            'salary_grade',
            'employment_status',
            'salary_component',
            'hr_prefix',
            'hr_tax',
            'religion',
            'hr_setting_page',
            'repair_module',
            'job_sheets',
            'add_job_sheet',
            'list_invoice',
            'add_invoice',
            'brands',
            'select_pump_operator_in_settlement',
            'repair_settings',
            'customer_interest_deduct_option'
        ];
    }


}
