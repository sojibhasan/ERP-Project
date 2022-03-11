<?php

namespace App\Exports;

use App\Account;
use Maatwebsite\Excel\Concerns\FromCollection;

class AccountExport implements FromCollection
{
    protected $business_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($business_id)
    {
        $this->business_id = $business_id;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Account::leftjoin('accounts as parent_account', 'accounts.parent_account_id', 'parent_account.id')
            ->leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $this->business_id)->select(
                'accounts.name',
                'accounts.account_number',
                'account_types.name as account_type',
                'account_groups.name as account_group',
                'accounts.note',
                'parent_account.name as parent_account',
                'accounts.is_main_account',
                'accounts.is_closed',
                'accounts.disabled',
                'accounts.default_account_id'
            )->orderBy('accounts.parent_account_id')->groupBy('accounts.id')->get();
    }
}
