<?php

namespace App\Utils;

use App\Contact;
use App\ContactGroup;

class ContactUtil
{

    /**
     * Returns Walk In Customer for a Business
     *
     * @param int $business_id
     *
     * @return array/false
     */
    public function getWalkInCustomer($business_id)
    {
        $contact = Contact::where('type', 'customer')
                    ->where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first()
                    ->toArray();

        if (!empty($contact)) {
            return $contact;
        } else {
            return false;
        }
    }
    /**
     * Returns Walk In Supplier for a Business
     *
     * @param int $business_id
     *
     * @return array/false
     */
    public function getDefaultSupplier($business_id)
    {
        $contact = Contact::where('type', 'supplier')
                    ->where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first();

        if (!empty($contact)) {
            return $contact->toArray();
        } else {
            return false;
        }
    }

    /**
     * Returns the customer group
     *
     * @param int $business_id
     * @param int $customer_id
     *
     * @return array
     */
    public function getCustomerGroup($business_id, $customer_id)
    {
        $cg = [];

        if (empty($customer_id)) {
            return $cg;
        }

        $contact = Contact::leftjoin('contact_groups as CG', 'contacts.customer_group_id', 'CG.id')
            ->where('contacts.id', $customer_id)
            ->where('CG.type', 'customer')
            ->where('contacts.business_id', $business_id)
            ->select('CG.*')
            ->first();

        return $contact;
    }
}
