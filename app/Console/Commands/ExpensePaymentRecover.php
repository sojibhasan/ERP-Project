<?php

namespace App\Console\Commands;

use App\AccountTransaction;
use App\Transaction;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpensePaymentRecover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:recover_expense_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to recover payment transaction deleted accidently';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transactions = Transaction::where('type', 'expense')->where('payment_status', 'paid')->get();
      
        $i = 0;
        foreach ($transactions as $transaction) {
            $transaction_payment = TransactionPayment::where('transaction_id', $transaction->id)->first();
            if (empty($transaction_payment)) {

                $account_transaction = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                    ->leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                    ->whereIn('account_groups.name', ['Cash Account', 'Card', "Cheques in Hand (Customer's)", 'Bank Account', 'Cash in Hand'])
                    ->where('transaction_id',  $transaction->id)
                    ->select('account_groups.name', 'account_transactions.operation_date', 'account_transactions.created_by', 'account_transactions.account_id')
                    ->first();

                if (!empty($account_transaction)) {
                    $account_id = null;
                    if ($account_transaction->name == 'Cash Account' || $account_transaction->name == 'Cash in Hand') {
                        $method = 'cash';
                    }
                    if ($account_transaction->name == 'Card') {
                        $method = 'card';
                    }
                    if ($account_transaction->name == "Cheques in Hand (Customer's)") {
                        $method = 'cheque';
                    }
                    if ($account_transaction->name == 'Bank Account') {
                        $method = 'bank_transfer';
                        $account_id = $account_transaction->account_id;
                    }
                    $payment_data = [
                        'amount' => $transaction->final_total,
                        'transaction_id' => $transaction->id,
                        'method' => $method,
                        'business_id' => $transaction->business_id,
                        'is_return' => 0,
                        'card_transaction_number' => null,
                        'card_number' => null,
                        'card_type' => null,
                        'card_holder_name' => null,
                        'card_month' => null,
                        'card_security' => null,
                        'cheque_number' => null,
                        'cheque_date' => Carbon::parse($account_transaction->operation_date)->format('Y-m-d'),
                        'note' => null,
                        'paid_on' => !empty($account_transaction->operation_date) ? $account_transaction->operation_date : Carbon::now()->toDateTimeString(),
                        'created_by' => $account_transaction->created_by,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => null,
                        'account_id' => !empty($account_id) ? $account_id : null
                    ];
                    $transaction_payment = TransactionPayment::create($payment_data);
                    $account_transaction->transaction_payment_id = $transaction_payment->id;
                    $account_transaction->save();
                    $i++;
                }
            }
        }
        print('Success! ' . $i . ' records recovered');
    }
}
