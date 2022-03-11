<?php

namespace Modules\TasksManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Reminder extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Reminder'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    protected $casts = ['other_pages' => 'array'];

    protected static function getOptionArray(){
        return  [
            'when_login' =>  __('tasksmanagement::lang.when_login'),
            'in_dashboard' =>  __('tasksmanagement::lang.in_dashboard'),
            'in_other_page' =>  __('tasksmanagement::lang.in_other_page')
        ];
    }
    protected static function getOtherPagesArray(){
        return  [
            'crm' =>  __('tasksmanagement::lang.crm'),
            'crmgroups' =>  __('tasksmanagement::lang.crmgroups'),
            'leads/leads' =>  __('tasksmanagement::lang.leads/leads'),
            'leads/import' =>  __('tasksmanagement::lang.leads/import'),
            'leads/day-count' =>  __('tasksmanagement::lang.leads/day-count'),
            'contacts' =>  __('tasksmanagement::lang.contacts'),
            'customer-group' =>  __('tasksmanagement::lang.customer-group'),
            'contacts/import' =>  __('tasksmanagement::lang.contacts/import'),
            'customer-reference' =>  __('tasksmanagement::lang.customer-reference'),
            'customer-statement' =>  __('tasksmanagement::lang.customer-statement'),
            'products' =>  __('tasksmanagement::lang.products'),
            'products/create' =>  __('tasksmanagement::lang.products/create'),
            'labels/show' =>  __('tasksmanagement::lang.labels/show'),
            'variation-templates' =>  __('tasksmanagement::lang.variation-templates'),
            'import-products' =>  __('tasksmanagement::lang.import-products'),
            'import-opening-stock' =>  __('tasksmanagement::lang.import-opening-stock'),
            'selling-price-group' =>  __('tasksmanagement::lang.selling-price-group'),
            'units' =>  __('tasksmanagement::lang.units'),
            'categories' =>  __('tasksmanagement::lang.categories'),
            'brands' =>  __('tasksmanagement::lang.brands'),
            'warranties' =>  __('tasksmanagement::lang.warranties'),
            'petro/dashboard' =>  __('tasksmanagement::lang.petro/dashboard'),
            'petro/tank-management' =>  __('tasksmanagement::lang.petro/tank-management'),
            'petro/pump-management' =>  __('tasksmanagement::lang.petro/pump-management'),
            'petro/pump-operators' =>  __('tasksmanagement::lang.petro/pump-operators'),
            'petro/daily-collection' =>  __('tasksmanagement::lang.petro/daily-collection'),
            'petro/settlement/create' =>  __('tasksmanagement::lang.petro/settlement/create'),
            'petro/settlement' =>  __('tasksmanagement::lang.petro/settlement'),
            'petro/dip-management' =>  __('tasksmanagement::lang.petro/dip-management'),
            'petro/issue-customer-bill' =>  __('tasksmanagement::lang.petro/issue-customer-bill'),
            'mpcs/form-set-1' =>  __('tasksmanagement::lang.mpcs/form-set-1'),
            'mpcs/F17' =>  __('tasksmanagement::lang.mpcs/F17'),
            'mpcs/F14B_F20_Forms' =>  __('tasksmanagement::lang.mpcs/F14B_F20_Forms'),
            'mpcs/F21' =>  __('tasksmanagement::lang.mpcs/F21'),
            'mpcs/F22_stock_taking' =>  __('tasksmanagement::lang.mpcs/F22_stock_taking'),
            'mpcs/forms-setting' =>  __('tasksmanagement::lang.mpcs/forms-setting'),
            'ran/gold-grade' =>  __('tasksmanagement::lang.ran/gold-grade'),
            'ran/gold-prices' =>  __('tasksmanagement::lang.ran/gold-prices'),
            'purchases' =>  __('tasksmanagement::lang.purchases'),
            'purchases/create' =>  __('tasksmanagement::lang.purchases/create'),
            'purchase-return' =>  __('tasksmanagement::lang.purchase-return'),
            'import-purchases' =>  __('tasksmanagement::lang.import-purchases'),
            'sales' =>  __('tasksmanagement::lang.sales'),
            'sales/create' =>  __('tasksmanagement::lang.sales/create'),
            'pos' =>  __('tasksmanagement::lang.pos'),
            'pos/create' =>  __('tasksmanagement::lang.pos/create'),
            'sales/drafts' =>  __('tasksmanagement::lang.sales/drafts'),
            'sales/quotations' =>  __('tasksmanagement::lang.sales/quotations'),
            'sales/customer/orders' =>  __('tasksmanagement::lang.sales/customer/orders'),
            'sales/customer/uploaded-orders' =>  __('tasksmanagement::lang.sales/customer/uploaded-orders'),
            'sell-return' =>  __('tasksmanagement::lang.sell-return'),
            'shipments' =>  __('tasksmanagement::lang.shipments'),
            'discount' =>  __('tasksmanagement::lang.discount'),
            'import-sales' =>  __('tasksmanagement::lang.import-sales'),
            'reserved-stocks' =>  __('tasksmanagement::lang.reserved-stocks'),
            'sells/over-limit-sales' =>  __('tasksmanagement::lang.sells/over-limit-sales'),
            'stock-transfers' =>  __('tasksmanagement::lang.stock-transfers'),
            'stock-transfers/create' =>  __('tasksmanagement::lang.stock-transfers/create'),
            'expenses' =>  __('tasksmanagement::lang.expenses'),
            'expenses/create' =>  __('tasksmanagement::lang.expenses/create'),
            'expense-categories' =>  __('tasksmanagement::lang.expense-categories'),
            'tasks-management/notes' =>  __('tasksmanagement::lang.tasks-management/notes'),
            'tasks-management/tasks' =>  __('tasksmanagement::lang.tasks-management/tasks'),
            'tasks-management/reminders' =>  __('tasksmanagement::lang.tasks-management/reminders'),
            'tasks-management/settings' =>  __('tasksmanagement::lang.tasks-management/settings'),
            'accounting-module/account' =>  __('tasksmanagement::lang.accounting-module/account'),
            'accounting-module/disabled-account' =>  __('tasksmanagement::lang.accounting-module/disabled-account'),
            'accounting-module/journal' =>  __('tasksmanagement::lang.accounting-module/journal'),
            'accounting-module/income-statement' =>  __('tasksmanagement::lang.accounting-module/income-statement'),
            'accounting-module/balance-sheet' =>  __('tasksmanagement::lang.accounting-module/balance-sheet'),
            'accounting-module/trial-balance' =>  __('tasksmanagement::lang.accounting-module/trial-balance'),
            'accounting-module/cash-flow' =>  __('tasksmanagement::lang.accounting-module/cash-flow'),
            'accounting-module/payment-account-report' =>  __('tasksmanagement::lang.accounting-module/payment-account-report'),
            'reports/product' =>  __('tasksmanagement::lang.reports/product'),
            'reports/payment-status' =>  __('tasksmanagement::lang.reports/payment-status'),
            'reports/management' =>  __('tasksmanagement::lang.reports/management'),
            'reports/activity' =>  __('tasksmanagement::lang.reports/activity'),
            'reports/contact' =>  __('tasksmanagement::lang.reports/contact'),
            'reports/trending-products' =>  __('tasksmanagement::lang.reports/trending-products'),
            'reports/user_activity' =>  __('tasksmanagement::lang.reports/user_activity'),
            'notification-templates/email-template' =>  __('tasksmanagement::lang.notification-templates/email-template'),
            'notification-templates/sms-template' =>  __('tasksmanagement::lang.notification-templates/sms-template'),
            'business/settings' =>  __('tasksmanagement::lang.business/settings'),
            'business-location' =>  __('tasksmanagement::lang.business-location'),
            'stores' =>  __('tasksmanagement::lang.stores'),
            'invoice-schemes' =>  __('tasksmanagement::lang.invoice-schemes'),
            'barcodes' =>  __('tasksmanagement::lang.barcodes'),
            'printers' =>  __('tasksmanagement::lang.printers'),
            'tax-rates' =>  __('tasksmanagement::lang.tax-rates'),
            'customer-settings' =>  __('tasksmanagement::lang.customer-settings'),
            'subscription' =>  __('tasksmanagement::lang.subscription'),
            'sms/list' =>  __('tasksmanagement::lang.sms/list'),
            'member/suggestions' =>  __('tasksmanagement::lang.member/suggestions'),
            'member-module/member-settings' =>  __('tasksmanagement::lang.member-module/member-settings'),
            'users' =>  __('tasksmanagement::lang.users'),
            'roles' =>  __('tasksmanagement::lang.roles')
        ];
    }

}