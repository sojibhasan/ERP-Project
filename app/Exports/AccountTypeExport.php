<?php

namespace App\Exports;

use App\AccountType;
use Maatwebsite\Excel\Concerns\FromCollection;

class AccountTypeExport implements FromCollection
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
        return AccountType::leftjoin('account_types as pat', 'account_types.parent_account_type_id', 'pat.id')
            ->where('account_types.business_id', $this->business_id)->select('account_types.name', 'pat.name as parent_account_type', 'account_types.default_account_type_id')->orderBy('account_types.id')->get();
    }
}
