<?php

namespace App\Imports;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Utils\TransactionUtil;
use Maatwebsite\Excel\Concerns\ToModel;

class AccountImport implements ToModel
{

    protected $business_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, $business_id)
    {
        $this->transactionUtil = $transactionUtil;
        $this->business_id = $business_id;
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Account([
            'business_id'     => $this->business_id,
            'name'     => $row[0],
            'account_number'     => $row[1],
            'account_type_id'     => !empty($row[2]) ? AccountType::where('business_id', $this->business_id)->where('name', $row[2])->first()->id : null,
            'asset_type'     => !empty($row[3]) ? AccountGroup::where('business_id', $this->business_id)->where('name', $row[3])->first()->id : null,
            'note'     => $row[4],
            'parent_account_id'     => !empty($row[5]) ? $this->transactionUtil->account_exist_return_id($row[5]) : null,
            'is_main_account'     => !empty($row[6]) ? 1 : 0,
            'is_closed'     => !empty($row[7]) ? 1 : 0,
            'disabled'     => !empty($row[8]) ? 1 : 0,
            'default_account_id'     => !empty($row[9]) ? $row[9] : null,
        ]);
    }
}
