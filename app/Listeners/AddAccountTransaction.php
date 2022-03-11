<?php

namespace App\Listeners;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\BusinessLocation;
use App\Category;
use App\ContactLedger;
use App\Events\TransactionPaymentAdded;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Fleet\Entities\Fleet;
use Modules\Property\Entities\PropertyAccountSetting;
use Modules\Property\Entities\PropertySellLine;

class AddAccountTransaction
{
    protected $moduleUtil;
    protected $transactionUtil;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function getAccountTypeIdOfAccount($account_id, $business_id)
    {
        $account_type = Account::join('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('accounts.id', $account_id)
            ->where('accounts.business_id', $business_id)
            ->select('account_types.id as account_type_id')
            ->first();
        return $account_type->account_type_id;
    }

    public function account_exist_return_id($account_name)
    {
        $business_id = request()->session()->get('business.id');
        $account = Account::where('name', 'like', '%' . $account_name . '%')->where('business_id', $business_id)->first();
        if (!empty($account)) {
            return $account->id;
        } else {
            return 0;
        }
    }


    public function getDefaultAccountId($account_name, $location_id)
    {
        $business_id = request()->session()->get('business.id');

        $account_id = null;
        $defualt_accounts = BusinessLocation::where('business_id', $business_id)->where('id',  $location_id)->first();
        if (!empty($defualt_accounts)) {
            $default_payment_accounts = (array) json_decode($defualt_accounts->default_payment_accounts);
            $account_id = $default_payment_accounts[$account_name]->account;
        }

        return $account_id;
    }
    public function getTransactionProductDetail($transaction_id, $transaction_type)
    {
        if ($transaction_type == 'purchase' || $transaction_type == 'purchase_return' || $transaction_type == 'opening_stock') {
            $product = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
                ->where('transactions.id', $transaction_id)
                ->select('products.id', 'enable_stock', 'stock_type', 'transactions.final_total as amount')->first();
        }

        if ($transaction_type == 'sell' || $transaction_type == 'sell_return') {
            $product = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transactions.id', $transaction_id)
                ->select('products.id', 'enable_stock', 'stock_type', DB::raw('SUM(transaction_sell_lines.quantity*variations.default_purchase_price) as amount'))->first();
        }
        return $product;
    }

    public function createCostofGoodsSoldTransaction($transaction, $sub_type = null, $type)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id', 'variations.default_purchase_price')
            ->get();
        foreach ($sell_lines as $sale) {
            $account_id = $this->account_exist_return_id('Cost of Goods Sold');
            if ($sale->quantity >= 0) { //not include pos page return 
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'cogs');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Cost of Goods Sold');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Cost of Goods Sold');
                    }
                }
                if (!empty($account_id)) {
                    $account_transaction_data = [
                        'amount' =>  abs($sale->quantity * $sale->default_purchase_price),
                        'account_id' => $account_id,
                        'type' => $type,
                        'sub_type' => $sub_type,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'sell_line_id' =>  $sale->id,
                        'note' => null
                    ];

                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }
    }
    public function createSaleIncomeTransaction($transaction, $sub_type = null, $type)
    {

        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id')
            ->get();


        foreach ($sell_lines as $sale) {
            $account_id = $this->account_exist_return_id('Sales Income');
            if ($sale->quantity >= 0) { //not include pos page return 
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'sale_income');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Sales Income');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Sales Income');
                    }
                }
                if (!empty($account_id)) {
                    $account_transaction_data = [
                        'amount' => abs($sale->quantity * $sale->unit_price),
                        'account_id' => $account_id,
                        'type' => $type,
                        'sub_type' => $sub_type,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' => $transaction->id,
                        'sell_line_id' =>  $sale->id,
                        'note' => null
                    ];
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }
    }

    public function manageStockAccount($transaction, $account_transaction_data, $trans_type, $amount, $sub_type = null)
    {
        $product_details = $this->getTransactionProductDetail($transaction->id,  $transaction->type);

        if ($transaction->type == 'sell') {
            $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $transaction->id)
                ->select('transaction_sell_lines.*', 'products.category_id', 'products.enable_stock', 'products.stock_type', 'products.sub_category_id', 'variations.default_purchase_price')
                ->get();

            foreach ($sell_lines as $sale) {
                if ($sale->quantity >= 0) { //not include pos page return 
                    $account_transaction_data['type'] = $trans_type;
                    if ($sale->enable_stock) {
                        $account_transaction_data['type'] = $trans_type;
                        $account_transaction_data['sub_type'] = $sub_type;
                        $account_transaction_data['amount'] = abs($sale->quantity * $sale->default_purchase_price);
                        if (!empty($sale->stock_type)) {
                            $account_transaction_data['account_id'] = $sale->stock_type;
                            $account_transaction_data['sell_line_id'] = $sale->id;
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                }
            }
        } else {
            $account_transaction_data['type'] = $trans_type;
            $account_transaction_data['amount'] = $amount;
            if ($product_details->enable_stock) {
                $account_transaction_data['type'] = $trans_type;
                $account_transaction_data['sub_type'] = $sub_type;
                $account_transaction_data['amount'] = $product_details->amount;
                if (!empty($product_details->stock_type)) {
                    $account_transaction_data['account_id'] = $product_details->stock_type;
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }

        return true;
    }


    public function getCategoryAccountId($category_id, $group)
    {
        $business_id = request()->session()->get('business.id');
        if ($group == 'cogs') {
            return Category::where('business_id', $business_id)->where('id', $category_id)->select('cogs_account_id')->first()->cogs_account_id;
        }
        if ($group == 'sale_income') {
            return Category::where('business_id', $business_id)->where('id', $category_id)->select('sales_income_account_id')->first()->sales_income_account_id;
        }
    }


    /**
     * manage stock (Raw material, Finish good) account transaction
     * $trans_type could be debit or credit 
     * 
     * @return void
     */

    public function updateAccountonPurchase($transaction, $account_transaction_data, $trans_type)
    {
        $product_details = $this->getTransactionProductDetail($transaction->id,  $transaction->type);

        if ($product_details->enable_stock) {
            $account_transaction_data['type'] = $trans_type;
            $account_transaction_data['amount'] = $transaction->final_total;
            if ($product_details->stock_type) {
                $raw_material_account_id = $this->getDefaultAccountId('raw_material_account', $transaction->location_id);
                $account_transaction_data['account_id'] = $raw_material_account_id;
            } else {
                $finish_good_account_id = $this->getDefaultAccountId('finished_goods_account', $transaction->location_id);
                $account_transaction_data['account_id'] = $finish_good_account_id;
            }
        }
        AccountTransaction::createAccountTransaction($account_transaction_data);

        return true;
    }

    public function getAmountofTransactionWithoutPosReturn($transaction)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*')
            ->get();
        $amount = 0;
        foreach ($sell_lines as $sale) {
            if ($sale->quantity >= 0) { //not include pos page return 
                $amount += ($sale->quantity * $sale->unit_price)  - $sale->line_discount_amount;
            }
        }

        return $amount;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TransactionPaymentAdded $event)
    {
        if (!empty($event->formInput['account_id'])) {
            $business_id = request()->session()->get('business.id');
            $asset_type_ids = AccountType::getAccountTypeIdOfType('Assets', $business_id);
            $account_type_id = $this->getAccountTypeIdOfAccount($event->formInput['account_id'], $business_id);
            $transaction_payment_details = TransactionPayment::where('id', $event->transactionPayment->id)->first();
            $transaction = Transaction::where('id',  $transaction_payment_details->transaction_id)->first();
            $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
            $account_payable_id = !empty($account_payable) ? $account_payable->id : 0;
            $account_receivable = Account::where('business_id', $business_id)->where('name', 'Accounts Receivable')->where('is_closed', 0)->first();
            $account_receivable_id = !empty($account_receivable) ? $account_receivable->id : 0;

            //Create new account transaction

            $account_transaction_data = [
                'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
                'amount' => $event->formInput['amount'],
                'account_id' => $event->formInput['account_id'],
                'type' => AccountTransaction::getAccountTransactionType($event->formInput['transaction_type']),
                'operation_date' =>  !empty($transaction->transaction_date) ? $transaction->transaction_date : date('Y-m-d H:i:s'),
                'created_by' => $event->transactionPayment->created_by,
                'transaction_id' => !empty($transaction) ? $transaction->id : null,
                'transaction_payment_id' =>  !empty($event->transactionPayment->id) ? $event->transactionPayment->id : null
            ];

            if ($event->transactionPayment->method == 'bank_transfer' || $event->transactionPayment->method == 'direct_bank_deposits' || $event->transactionPayment->method == 'cheque') {
                $account_transaction_data['operation_date'] = $event->transactionPayment->cheque_date;
            }

            if ($event->transactionPayment->pay_supplier_due && $event->formInput['transaction_type'] == 'purchase') {
                $account_transaction_data['amount'] =  $event->formInput['amount'];
                $account_transaction_data['type'] = 'credit';
                $account_transaction_data['account_id'] = $event->formInput['account_id'];
                AccountTransaction::createAccountTransaction($account_transaction_data);

                $account_transaction_data['type'] = 'debit';
                $account_transaction_data['account_id'] = $account_payable_id;

                AccountTransaction::createAccountTransaction($account_transaction_data);
                return true;
            }


            // if purhcase then change type to credit
            if ($event->formInput['transaction_type'] == 'purchase' || $event->formInput['transaction_type'] == 'property_purchase') {
                $amount_paid = (float) $event->formInput['amount'];
                if ($amount_paid > 0.0) {
                    $account_transaction_data['amount'] =  $amount_paid;
                    $account_transaction_data['type'] = 'credit';
                    $account_transaction_data['account_id'] = $event->formInput['account_id'];

                    AccountTransaction::createAccountTransaction($account_transaction_data);
                    ContactLedger::createContactLedger($account_transaction_data);
                    $account_transaction_data['type'] = 'debit';
                    ContactLedger::createContactLedger($account_transaction_data);
                } else {
                    $account_transaction_data['amount'] =  $transaction->final_total;
                    $account_transaction_data['type'] = 'credit';
                    $account_transaction_data['account_id'] = $account_payable_id;

                    AccountTransaction::createAccountTransaction($account_transaction_data);
                    $account_transaction_data['sub_type'] = 'payment';
                    ContactLedger::createContactLedger($account_transaction_data);
                }
            }

            // if expense then change type to credit
            if ($event->formInput['transaction_type'] == 'expense') {
                if (in_array($account_type_id,  $asset_type_ids)) {  //if account type is asset
                    $account_transaction_data['type'] = 'credit';
                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    if (!empty($transaction->controller_account)) {
                        $account_payable_id = $transaction->controller_account;
                    }
                    $account_transaction_data['type'] = 'debit';
                    $account_transaction_data['account_id'] = $account_payable_id;

                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }

            // if sell_return then change type to credit
            if ($event->formInput['transaction_type'] == 'sell_return') {
                $this->createCostofGoodsSoldTransaction($transaction, 'ledger_show', 'credit');
                $this->createSaleIncomeTransaction($transaction, null, 'debit');
                $this->manageStockAccount($transaction, $account_transaction_data, 'debit', $event->formInput['amount']);
                ContactLedger::createContactLedger($account_transaction_data);
            }

            // if sell then change type to debit
            if ($event->formInput['transaction_type'] == 'sell') {
                if ((in_array($account_type_id,  $asset_type_ids) || $event->formInput['method'] == 'card')) {  //if account type is asset
                    $amount = $this->getAmountofTransactionWithoutPosReturn($transaction);
                    $account_transaction_data['amount'] = $amount;
                    if ($transaction->is_credit_sale == '1') {
                        $account_transaction_data['amount'] = $event->formInput['amount'];
                    }
                    $account_transaction_data['type'] = 'debit';
                    $account_transaction_data['sub_type'] = 'ledger_show';
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                    $account_transaction_data['sub_type'] = 'payment';
                    ContactLedger::createContactLedger($account_transaction_data);
                }

                $this->manageStockAccount($transaction, $account_transaction_data, 'credit', $transaction->final_total);
                $this->createCostofGoodsSoldTransaction($transaction, null, 'debit');
                if ($transaction->is_credit_sale == '1') {
                    $this->createSaleIncomeTransaction($transaction, null, 'credit');
                } else {
                    $this->createSaleIncomeTransaction($transaction, 'ledger_show', 'credit');
                    $account_transaction_data['type'] = 'credit';
                    $account_transaction_data['sub_type'] = 'payment';
                    ContactLedger::createContactLedger($account_transaction_data);
                }
            }
            // if property sell then change type to debit
            if ($event->formInput['transaction_type'] == 'property_sell') {
                $transaction_sell_line = PropertySellLine::where('transaction_id', $transaction->id)->first();
                $account_transaction_data['type'] = 'debit';
                $account_transaction_data['amount'] = $event->formInput['amount'];
                $account_transaction_data['account_id'] = $event->formInput['account_id'];

                AccountTransaction::createAccountTransaction($account_transaction_data);

                $account_transaction_data['type'] = 'credit';
                ContactLedger::createContactLedger($account_transaction_data);

                $property_accounts = PropertyAccountSetting::where('property_id', $transaction_sell_line->property_id)->first();

                if (!empty($property_accounts->account_receivable_account_id)) {
                    $account_transaction_data['type'] = 'credit';
                    $account_transaction_data['account_id'] =  $property_accounts->account_receivable_account_id;
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }

            // if purhcase_return then change type to debit
            if ($event->formInput['transaction_type'] == 'purchase_return') {
                if (in_array($account_type_id,  $asset_type_ids)) {  //if account type is asset
                    $account_transaction_data['type'] = 'debit';
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
                $this->manageStockAccount($transaction, $account_transaction_data, 'credit', $event->formInput['amount']);
            }

            //if route operation
            if ($event->formInput['transaction_type'] == 'route_operation') {
                $fleet = Fleet::find($transaction->fleet_id);
                $account_transaction_data['type'] = 'debit';
                AccountTransaction::createAccountTransaction($account_transaction_data);
                
                $account_transaction_data['type'] = 'credit';
                $account_transaction_data['account_id'] = $fleet->income_account_id;
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
        }
    }
}
