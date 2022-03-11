<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Contact'; 

    use Notifiable;

    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function scopeOnlySuppliers($query)
    {
        return $query->whereIn('contacts.type', ['supplier', 'both']);
    }

    public function scopeOnlyCustomers($query)
    {
        return $query->whereIn('contacts.type', ['customer', 'both']);
    }
    public function scopeOnlyActive($query)
    {
        return $query->where('contacts.active', 1);
    }

     /**
     * Get all of the contacts's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany('App\DocumentAndNote', 'notable');
    }

       /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Return list of contact dropdown for a business
     *
     * @param $business_id int
     * @param $exclude_default = false (boolean)
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function contactDropdown($business_id, $exclude_default = false, $prepend_none = true, $append_id = true)
    {
        $query = Contact::where('business_id', $business_id);
        if ($exclude_default) {
            $query->where('is_default', 0);
        }

        if ($append_id) {
            $query->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contact_id, ')')) AS supplier"),
                'id'
                    );
        } else {
            $query->select(
                'id',
                DB::raw("IF (supplier_business_name IS not null, CONCAT(name, ' (', supplier_business_name, ')'), name) as supplier")
            );
        }
        
        $contacts = $query->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $contacts = $contacts->prepend(__('lang_v1.none'), '');
        }

        return $contacts;
    }
    
    /**
     * Return list of searched contact dropdown for a business
     *
     * @param $business_id int
     * @param $exclude_default = false (boolean)
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function customersSearchRecord($business_id, $exclude_default = false, $customer_name = '', $prepend_none = true, $append_id = true)
    {
        $query = Contact::where('business_id', $business_id)->where('name','like','%'.$customer_name."%");
        if ($exclude_default) {
            $query->where('is_default', 0);
        }

        if ($append_id) {
            $query->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contact_id, ')')) AS supplier"),
                'id'
                    );
        } else {
            $query->select(
                'id',
                DB::raw("IF (supplier_business_name IS not null, CONCAT(name, ' (', supplier_business_name, ')'), name) as supplier")
            );
        }
        
        $contacts = $query->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $contacts = $contacts->prepend(__('lang_v1.none'), '');
        }
        
        return $contacts;
    }

    /**
     * Return list of suppliers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function suppliersDropdown($business_id, $prepend_none = true, $append_id = true)
    {
        $all_contacts = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['supplier', 'both']);

        if ($append_id) {
            $all_contacts->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contact_id, ')')) AS supplier"),
                'id'
                    );
        } else {
            $all_contacts->select(
                'id',
                DB::raw("CONCAT(name, ' (', supplier_business_name, ')') as supplier")
                );
        }

        $suppliers = $all_contacts->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $suppliers = $suppliers->prepend(__('lang_v1.none'), '');
        }

        return $suppliers;
    }
    /**
     * Return list of suppliers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function suppliersDropdownByType($business_id, $prepend_none = true, $append_id = true, $type)
    {
        $all_contacts = Contact::where('business_id', $business_id)
                        ->whereIn('type', $type);

        if ($append_id) {
            $all_contacts->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contact_id, ')')) AS supplier"),
                'id'
                    );
        } else {
            $all_contacts->select(
                'id',
                DB::raw("CONCAT(name, ' (', supplier_business_name, ')') as supplier")
                );
        }

        $suppliers = $all_contacts->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $suppliers = $suppliers->prepend(__('lang_v1.all'), '');
        }

        return $suppliers;
    }

    /**
     * Return list of suppliers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function propertyCustomerDropdown($business_id, $prepend_none = true, $append_nic = true)
    {
        $all_contacts = Contact::where('business_id', $business_id)
                        ->where('type', 'customer');

        if ($append_nic) {
            $all_contacts->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(tax_number, ''), '(', contact_id, ')')) AS customer"),
                'id'
                    );
        } else {
            $all_contacts->select(
                'id',
                DB::raw("CONCAT(name, ' (', supplier_business_name, ')') as customer")
                );
        }

        $suppliers = $all_contacts->pluck('customer', 'id');

        //Prepend none
        if ($prepend_none) {
            $suppliers = $suppliers->prepend(__('lang_v1.all'), '');
        }

        return $suppliers;
    }

    /**
     * Return list of customers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     *
     * @return array users
     */
    public static function customersDropdown($business_id, $prepend_none = true, $append_id = true)
    {
        $all_contacts = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['customer', 'both'])->onlyActive();

        if ($append_id) {
            $all_contacts->select(
                DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' (', contact_id, ')')) AS customer"),
                'id'
                );
        } else {
            $all_contacts->select('id', DB::raw("name as customer"));
        }

        $customers = $all_contacts->pluck('customer', 'id');

        //Prepend none
        if ($prepend_none) {
            $customers = $customers->prepend(__('lang_v1.none'), '');
        }

        return $customers;
    }

    /**
     * Return list of contact type.
     *
     * @param $prepend_all = false (boolean)
     * @return array
     */
    public static function typeDropdown($prepend_all = false)
    {
        $types = [];

        if ($prepend_all) {
            $types[''] = __('lang_v1.all');
        }

        $types['customer'] = __('report.customer');
        $types['supplier'] = __('report.supplier');
        $types['both'] = __('lang_v1.both_supplier_customer');

        return $types;
    }

    /**
     * Return list of contact type by permissions.
     *
     * @return array
     */
    public static function getContactTypes()
    {
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

        return $types;
    }
}
