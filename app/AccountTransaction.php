<?php

namespace App;

use App\Utils\TransactionUtil;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Account;

class AccountTransaction extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'operation_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id')->withTrashed();;
    }

    /**
     * Gives account transaction type from payment transaction type
     * @param  string $payment_transaction_type
     * @return string
     */
    public static function getAccountTransactionType($tansaction_type)
    {
        $account_transaction_types = [
            'sell' => 'credit',
            'purchase' => 'credit', // change debit to credit as required by syzygy
            'expense' => 'debit',
            'purchase_return' => 'credit',
            'sell_return' => 'debit',
            'property_purchase' => 'credit',
            'property_sell' => 'credit',
            'route_operation' => 'credit'
        ];

        return $account_transaction_types[$tansaction_type];
    }

    /**
     * Creates new account transaction
     * @return obj
     */
    public static function createAccountTransaction($data)
    {

        if (!empty($data['interest'])){
            $transaction_data['interest'] = $data['interest'];
        }
        $account = Account::where('id', $data['account_id'])->select('business_id')->first();
        $transaction_data = [
            'amount' => $data['amount'],
            'account_id' => $data['account_id'],
            'business_id' => $account['business_id'],
            'type' => $data['type'],
            'sub_type' => !empty($data['sub_type']) ? $data['sub_type'] : null,
            'operation_date' => !empty($data['operation_date']) ? $data['operation_date'] : \Carbon::now(),
            'created_by' =>  !empty($data['created_by']) ? $data['created_by'] : Auth::user()->id,
            'transaction_id' => !empty($data['transaction_id']) ? $data['transaction_id'] : null,
            'transaction_payment_id' => !empty($data['transaction_payment_id']) ? $data['transaction_payment_id'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'cheque_number' => !empty($data['cheque_number']) ? $data['cheque_number'] : null,
            'attachment' => !empty($data['attachment']) ? $data['attachment'] : null,
            'transfer_transaction_id' => !empty($data['transfer_transaction_id']) ? $data['transfer_transaction_id'] : null,
            'transaction_sell_line_id' => !empty($data['transaction_sell_line_id']) ? $data['transaction_sell_line_id'] : null,
            'sell_line_id' => !empty($data['sell_line_id']) ? $data['sell_line_id'] : null,
            'purchase_line_id' => !empty($data['purchase_line_id']) ? $data['purchase_line_id'] : null,
            'income_type' => !empty($data['income_type']) ? $data['income_type'] : null,
            'installment_id' => !empty($data['installment_id']) ? $data['installment_id'] : null,
            'interest' => !empty($data['interest']) ? $data['interest'] : null
        ];
        $account_transaction = AccountTransaction::create($transaction_data);

        return $account_transaction;
    }

    /**
     * Updates transaction payment from transaction payment
     * @param  obj $transaction_payment
     * @param  array $inputs
     * @param  string $transaction_type
     * @return string
     */
    public static function updateAccountTransaction($transaction_payment, $transaction_type)
    {

        $account_id = $transaction_payment->account_id;
        $transaction_id = $transaction_payment->transaction_id;
        $transaction = Transaction::find($transaction_id);
        if (!empty($transaction_payment->parent_id)) {
            $account_transaction = AccountTransaction::where(
                'transaction_payment_id',
                $transaction_payment->parent_id
            )->first();
            $transaction_payment = TransactionPayment::find($transaction_payment->parent_id);
        } else {
            $account_transaction = AccountTransaction::where(
                'transaction_payment_id',
                $transaction_payment->id
            )->first();
        }

        if ($transaction_type == 'purchase') {
            //update contact ledger transaction
            ContactLedger::where(
                'transaction_id',
                $transaction_payment->transaction_id
            )->where('transaction_payment_id',  $transaction_payment->id)
                ->update(['amount' => $transaction_payment->amount]);
        }
        if ($transaction_type == 'sell' || $transaction_type == 'property_sell') {
            //update contact ledger transaction
            ContactLedger::where('transaction_payment_id',  $transaction_payment->id)
                ->update(['amount' => $transaction_payment->amount]);
        }

        if (!empty($account_transaction)) {
            $account_transaction->amount = $transaction_payment->amount;
            $account_transaction->account_id = $account_id;
            $account_transaction->operation_date = $transaction_payment->paid_on;
            $account_transaction->save();

            return $account_transaction;
        } else {
            $accnt_trans_data = [
                'amount' => $transaction_payment->amount,
                'account_id' => $account_id,
                'type' => self::getAccountTransactionType($transaction_type),
                'operation_date' => $transaction_payment->paid_on,
                'created_by' => $transaction_payment->created_by,
                'transaction_id' => $transaction_payment->transaction_id,
                'transaction_payment_id' => $transaction_payment->id
            ];

            //If change return then set type as debit
            if ($transaction_payment->transaction->type == 'sell' && $transaction_payment->is_return == 1) {
                $accnt_trans_data['type'] = 'debit';
            }

            self::createAccountTransaction($accnt_trans_data);
            $accnt_trans_data['contact_id'] = $transaction->contact_id;
            ContactLedger::createContactLedger($accnt_trans_data);
        }
    }

    public function transfer_transaction()
    {
        return $this->belongsTo(\App\AccountTransaction::class, 'transfer_transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }
}
