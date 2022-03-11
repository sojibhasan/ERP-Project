<?php

namespace App\Console\Commands;

use App\Transaction;
use App\Utils\TransactionUtil;
use Illuminate\Console\Command;
use PhpParser\Node\Stmt\Foreach_;

class UpdatePaymentStatusSell extends Command
{
    protected $transactionUtil;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_payment_status_sell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(  TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sells = Transaction::where('type', 'sell')->where('business_id', 8)->select('id')->get();
        
        $i = 1;
        foreach($sells as $sell){
            $this->transactionUtil->updatePaymentStatus($sell->id);
            $i++;
        }
        print($i .' trnasactions updated');
    }
}
