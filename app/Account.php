<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use SoftDeletes;

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Account';

    protected $guarded = ['id'];

    public static function forDropdown($business_id, $prepend_none, $closed = false)
    {
        $query = Account::where('business_id', $business_id);

        if (!$closed) {
            $query->where('is_closed', 0);
        }

        $dropdown = $query->pluck('name', 'id');
        if ($prepend_none) {
            $dropdown->prepend(__('lang_v1.none'), '');
        }

        return $dropdown;
    }

    public static function forDropdownStockType($business_id, $prepend_none, $closed = false)
    {
        $query = Account::where('business_id', $business_id)->whereIn('name', ['Stock Account', 'Raw Material Account', 'Finished Goods Account']);

        if (!$closed) {
            $query->where('is_closed', 0);
        }

        $dropdown = $query->pluck('name', 'id');
        if ($prepend_none) {
            $dropdown->prepend(__('lang_v1.none'), '');
        }

        return $dropdown;
    }

    /**
     * Scope a query to only include not closed accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotClosed($query)
    {
        return $query->where('is_closed', 0);
    }

    /**
     * Scope a query to only include non capital accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function scopeNotCapital($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->where('account_type', '!=', 'capital');
    //         $q->orWhereNull('account_type');
    //     });
    // }

    public static function accountTypes()
    {
        return [
            '' => __('account.not_applicable'),
            'saving_current' => __('account.saving_current'),
            'capital' => __('account.capital')
        ];
    }

    public function account_type()
    {
        return $this->belongsTo(\App\AccountType::class, 'account_type_id');
    }

    public function sub_accounts()
    {
        return $this->hasMany(\App\Account::class, 'parent_account_id');
    }

    public static function AssetTypeAccountGroupActive()
    {
        return  array(
            '1' => 'Raw material Account',
            '2' => 'Finished Goods Account',
            '3' => 'Other Stocks',
            '4' => 'Bank Account',
            '5' => 'Cash Account',
            '6' => 'Cheques in Hand (Customer’s)',
            '7' => 'Card',
            '8' => 'COGS',
            '9' => 'Sales Income',
        );
    }
    public static function AssetTypeAccountGroupNoneActive()
    {
        return  array(
            '4' => 'Bank Account',
            '5' => 'Cash Account',
            '6' => 'Cheques in Hand (Customer’s)',
            '7' => 'Card'
        );
    }

    public static function getAccountTypeIdByName($account_type_name)
    {
        $business_id = request()->session()->get('user.business_id');

        return AccountType::where('business_id', $business_id)->where('name', $account_type_name)->first()->id;
    }

    public static function getAccountByAccountGroupId($group_id)
    {
        $business_id = request()->session()->get('user.business_id');

        return Account::where('business_id', $business_id)->where('asset_type', $group_id)->where('is_main_account', 0)->pluck('name', 'id');
    }

    public static function getAccountByAccountTypeId($account_type_id)
    {
        $business_id = request()->session()->get('user.business_id');

        return Account::where('business_id', $business_id)->where('account_type_id', $account_type_id)->where('is_main_account', 0)->pluck('name', 'id');
    }

    public static function getAccountByAccountTypeName($account_type_name)
    {
        $business_id = request()->session()->get('user.business_id');

        return Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_types.name', $account_type_name)
            ->where('is_main_account', 0)
            ->pluck('accounts.name', 'accounts.id');
    }

    public static function getAccountByAccountName($account_name)
    {
        $business_id = request()->session()->get('business.id');
        $account = Account::where(DB::raw("REPLACE(`name`, '  ', ' ')"), $account_name)->where('business_id', $business_id)->first();

        return $account;
    }

    /* return all sub accounts if exist */
    public static function getSubAccountOrParentAccountByName($account_name)
    {
        $business_id = request()->session()->get('business.id');
        $this_account = Account::where('name', $account_name)->where('business_id', $business_id)->first();
        if (!empty($this_account->is_main_account)) { //if main account then return sub accounts
            $sub_accounts = Account::where('business_id', $business_id)->where('parent_account_id', $this_account->id)->pluck('name', 'id');
            return  $sub_accounts;
        }
        return $this_account->pluck('name', 'id');
    }


    public static function getSubAccountBalanceByMainAccountId($prent_account_id, $start_date = null, $end_date = null)
    {
        $business_id = request()->session()->get('user.business_id');

        $sub_account_balance_total = 0;
        $asset_sub_accounts = Account::where('business_id', $business_id)->where('parent_account_id', $prent_account_id)->get();
        foreach ($asset_sub_accounts as $asset_sub_account) {
            $sub_account_balance_total += Account::getAccountBalance($asset_sub_account->id, $start_date, $end_date);
        }

        return $sub_account_balance_total;
    }

    public static function checkInsufficientBalance($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $account_group = AccountGroup::getAccountGroupByAccountId($id);
        $check_insufficient = false;
        if (!empty($account_group)) {
            if ($account_group->name == 'Cash Account' || $account_group->name == "Cheques in Hand (Customer's)" || $account_group->name == 'Card') {
                $check_insufficient = true;
            }
        }
        return $check_insufficient;
    }

    /**
     * Calculates account balance.
     * @param  int $id
     * @return float
     */
    static function getAccountBalance($id, $start_date = null, $end_date = null, $get_previous = false, $account_book = false, $is_daily_report = false)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $account_type_id = Account::where('id', $id)->first()->account_type_id;
        $account_type_name = AccountType::where('id', $account_type_id)->first();
        $business_id = session()->get('user.business_id');
        if (strpos($account_type_name, "Assets") !== false || strpos($account_type_name, "Expenses") !== false) {

            $account_query = Account::leftjoin(
                'account_transactions as AT',
                'AT.account_id',
                '=',
                'accounts.id'
            )
                ->whereNull('AT.deleted_at')
                ->where('accounts.business_id', $business_id)
                ->where('accounts.id', $id);

            if (!empty($start_date) && !empty($end_date) && !$get_previous) {
                $account_query->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }
            if ($get_previous && !empty($start_date)) {
                if ($account_book) {
                    $account_query->whereDate('operation_date', '<', $start_date);
                } elseif ($is_daily_report) {
                    $account_query->whereDate('operation_date', '<=', $end_date);
                } else {
                    $account_query->whereDate('operation_date', '<=', $start_date);
                }
            }
            $account_query->where('is_closed', 0);
            $account = $account_query->select(
                'accounts.*',
                DB::raw("SUM( IF(AT.type='credit', -1 * amount, amount) ) as balance")
            )
                ->first();
        } else {
            $account_query = Account::leftjoin(
                'account_transactions as AT',
                'AT.account_id',
                '=',
                'accounts.id'
            )
                ->whereNull('AT.deleted_at')
                ->where('accounts.business_id', $business_id)
                ->where('accounts.id', $id);
            if (!empty($start_date) && !empty($end_date) && !$get_previous) {
                $account_query->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }
            if ($get_previous && !empty($start_date)) {
                if ($account_book) {
                    $account_query->whereDate('operation_date', '<', $start_date);
                } elseif ($is_daily_report) {
                    $account_query->whereDate('operation_date', '<=', $end_date);
                } else {
                    $account_query->whereDate('operation_date', '<=', $start_date);
                }
            }
            $account_query->where('is_closed', 0);
            $account = $account_query->select(
                'accounts.*',
                DB::raw("SUM( IF(AT.type='debit',-1 * amount,  amount) ) as balance")
            )
                ->first();
        }

        return $account ? $account->balance : 0;
    }
    /**
     * Calculates account balance.
     * @param  int $id
     * @return float
     */
    static function getStockGroupAccountBalanceByTransactionType($type, $start_date = null, $end_date = null, $get_previous = false)
    {
        $business_id = session()->get('user.business_id');
        $balance = 0;
        $FGA_group_id = AccountGroup::getGroupByName('Finished Goods Account')->id;
        $OS_group_id = AccountGroup::getGroupByName('Other Stocks')->id;
        $RMA_group_id = AccountGroup::getGroupByName('Raw Material Account')->id;

        $stock_group_id_array = [$FGA_group_id, $OS_group_id, $RMA_group_id];

        $account_query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )->leftjoin(
            'transactions',
            'AT.transaction_id',
            '=',
            'transactions.id'
        )
            ->where('accounts.business_id', $business_id)
            ->where('transactions.type', $type)
            ->whereIn('accounts.asset_type', $stock_group_id_array)
            ->whereNull('AT.deleted_at')
            ->where('is_closed', 0);
        if (!empty($start_date) && !empty($end_date) && !$get_previous && $type != 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '>=', $start_date);
            $account_query->whereDate('AT.operation_date', '<=', $end_date);
        }
        if ($get_previous && $type != 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '<', $start_date);
        }
        if ($type == 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '<=', $end_date);
        }

        $balance = $account_query->select(
            DB::raw("SUM( IF(AT.type='credit', -1 * amount, amount) ) as balance")
        )
            ->first();


        return $balance ? $balance->balance : 0;
    }
    /**
     * Calculates account balance.
     * @param  int $id
     * @return float
     */
    static function getStockGroupAccountBalanceByTransactionTypeAndCategory($type, $sub_cat_id, $start_date = null, $end_date = null, $get_previous = false, $get_qty = false)
    {
        $business_id = session()->get('user.business_id');
        $balance = 0;
        $FGA_group_id = AccountGroup::getGroupByName('Finished Goods Account')->id;
        $OS_group_id = AccountGroup::getGroupByName('Other Stocks')->id;
        $RMA_group_id = AccountGroup::getGroupByName('Raw Material Account')->id;

        $stock_group_id_array = [$FGA_group_id, $OS_group_id, $RMA_group_id];

        $account_query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )->leftjoin(
            'transactions',
            'AT.transaction_id',
            '=',
            'transactions.id'
        );
        if ($type == 'sell') {
            $account_query->leftjoin(
                'transaction_sell_lines',
                'transactions.id',
                'transaction_sell_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'transaction_sell_lines.product_id',
                    'products.id'
                );
        }
        if ($type == 'purchase' || $type == 'opening_stock') {
            $account_query->leftjoin(
                'purchase_lines',
                'transactions.id',
                'purchase_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'purchase_lines.product_id',
                    'products.id'
                );
        }
        if ($type == 'stock_adjustment') {
            $account_query->leftjoin(
                'stock_adjustment_lines',
                'transactions.id',
                'stock_adjustment_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'stock_adjustment_lines.product_id',
                    'products.id'
                );
        }
        $account_query->where('products.sub_category_id', $sub_cat_id)->groupBy('products.sub_category_id');

        $account_query->where('accounts.business_id', $business_id)
            ->where('transactions.type', $type)
            ->whereIn('accounts.asset_type', $stock_group_id_array)
            ->whereNull('AT.deleted_at')
            ->where('is_closed', 0);
        if (!empty($start_date) && !empty($end_date) && !$get_previous && $type != 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '>=', $start_date);
            $account_query->whereDate('AT.operation_date', '<=', $end_date);
        }
        if ($get_previous && $type != 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '<', $start_date);
        }
        if ($type == 'opening_stock') {
            $account_query->whereDate('AT.operation_date', '<', $end_date);
        }

        if (!$get_qty) {
            $balance = $account_query->select(
                DB::raw("SUM(AT.amount) as balance")
            )->first();
        }
        if ($get_qty) {
            if ($type == 'sell') {
                $balance = $account_query->groupBy('products.sub_category_id')->select(
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as balance')
                )->first();
            }
            if ($type == 'purchase' || $type == 'opening_stock') {
                $balance = $account_query->select(
                    DB::raw("SUM(purchase_lines.quantity) as balance")
                )->first();
            }
            if ($type == 'stock_adjustment') {
                $balance = $account_query->select(
                    DB::raw("SUM(stock_adjustment_lines.quantity) as balance")
                )->first();
            }
        }



        return $balance ? $balance->balance : 0;
    }


    public static function getAccountGroupBalanceByType($account_group_id, $type, $start_date, $end_date, $is_previous = false)
    {
        $balance = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->where('accounts.asset_type', $account_group_id)
            ->where('accounts.is_main_account', 0)
            ->where('type', $type)
            ->where(
                function ($query) {
                    $query->where('sub_type', '!=', 'opening_balance')
                        ->orWhereNull('sub_type');
                }
            );

        // $balance->where('sub_type', '!=', 'opening_balance');
        $amount = 0;
        if (!$is_previous) {
            $balance->whereDate('operation_date', '>=', $start_date);
            $balance->whereDate('operation_date', '<=', $end_date);
            $amount = $balance->sum('amount');
        } else {
            $balance->whereDate('operation_date', '<', $start_date);
            $amount = $balance->sum('amount');
        }

        if (!empty($amount)) {
            return $amount;
        }
        return 0;
    }

    public static function getAccountBalanceByType($account_id, $type, $start_date, $end_date, $is_previous = false, $opening_balance_only = false)
    {
        $balance = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
            ->where('accounts.id', $account_id)
            ->where('accounts.is_main_account', 0)
            ->where('account_transactions.type', $type);
        if ($opening_balance_only) {
            $balance->whereIn('transactions.type', ['opening_balance']);
        } else {
            $balance->whereNotIn('transactions.type', ['opening_balance']);
        }

        $amount = 0;
        if (!$opening_balance_only) {
            if (!$is_previous) {
                $balance->whereDate('operation_date', '>=', $start_date);
                $balance->whereDate('operation_date', '<=', $end_date);
            } else {
                $balance->whereDate('operation_date', '<', $start_date);
            }
        }
        $amount = $balance->sum('amount');
        if (!empty($amount)) {
            return $amount;
        }
        return 0;
    }

    public static function getAccountGroupOpeningBalanceByType($account_group_id, $type, $start_date, $end_date)
    {
        $balance = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->where('accounts.asset_type', $account_group_id)
            ->where('accounts.is_main_account', 0)
            ->where('type', $type);

        $amount = 0;

        $balance->where('sub_type', '=', 'opening_balance');
        // $balance->whereDate('operation_date', '<', $start_date);
        $amount = $balance->sum('amount');


        if (!empty($amount)) {
            return $amount;
        }
        return 0;
    }
}
