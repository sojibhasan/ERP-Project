<?php

namespace App\Imports;

use App\AccountType;
use Maatwebsite\Excel\Concerns\ToModel;

class AccountTypeImport implements ToModel
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
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new AccountType([
            'business_id'     => $this->business_id,
            'name'     => $row[0],
            'parent_account_type_id'     => !empty($row[1]) ? AccountType::where('business_id', $this->business_id)->where('name', $row[1])->first()->id : null,
            'default_account_type_id'     => $row[2],
        ]);
    }
}
