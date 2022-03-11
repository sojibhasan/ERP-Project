<?php

namespace App\Exports;

use App\AccountGroup;
use Maatwebsite\Excel\Concerns\FromCollection;

class AccountGroupExport implements FromCollection
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
        return AccountGroup::leftjoin('account_types', 'account_groups.account_type_id', 'account_types.id')
            ->where('account_groups.business_id', $this->business_id)->select('account_groups.name', 'account_types.name as account_type', 'account_groups.note', 'account_groups.default_account_group_id')->orderBy('account_groups.id')->get();
    }
}
