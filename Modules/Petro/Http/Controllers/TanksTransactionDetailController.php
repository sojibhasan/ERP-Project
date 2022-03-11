<?php



namespace Modules\Petro\Http\Controllers;



use App\Business;

use App\BusinessLocation;

use App\Product;

use App\Transaction;

use App\Utils\ModuleUtil;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;



class TanksTransactionDetailController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)

    {

        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
        DB::enableQueryLog();
        set_time_limit(0);

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
          
            $transactionWithpurchaseLine = Transaction::leftjoin('tank_purchase_lines', function ($join) {

                $join->on('transactions.id', 'tank_purchase_lines.transaction_id')->where('tank_purchase_lines.quantity', '!=', 0);

            })

                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                ->join('fuel_tanks', function ($join) {

                    $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                })

                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->where('transactions.business_id', $business_id)

                // ->where('tank_purchase_lines.quantity', '!=', 0)

                ->select(

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.transaction_date',

                    'transactions.created_at',

                    'transactions.type',

                    DB::raw('SUM(tank_purchase_lines.quantity) as purchase_qty'),

                    DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['transactions.id', 'products.id', 'fuel_tanks.id']);

            $query = Transaction::leftjoin('tank_purchase_lines', function ($join) {

                $join->on('transactions.id', 'tank_purchase_lines.transaction_id')->where('tank_purchase_lines.quantity', '!=', 0);

            })

                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                ->join('fuel_tanks', function ($join) {

                    $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                })

                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->where('transactions.business_id', $business_id)

                // ->where('tank_purchase_lines.quantity', '!=', 0)

                ->select(

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.transaction_date',

                    'transactions.created_at',

                    'transactions.type',

                    DB::raw('SUM(tank_purchase_lines.quantity) as purchase_qty'),

                    DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['transactions.id', 'products.id', 'fuel_tanks.id'])

                ->orderby('transactions.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $query->whereDate('transactions.transaction_date', '>=', request()->start_date);

                $query->whereDate('transactions.transaction_date', '<=', request()->end_date);

                $transactionWithpurchaseLine->whereDate('transactions.transaction_date', '>=', request()->start_date);

                $transactionWithpurchaseLine->whereDate('transactions.transaction_date', '<=', request()->end_date);

            }

            if (!empty(request()->location_id)) {

                $query->where('transactions.location_id', request()->location_id);

                $transactionWithpurchaseLine->where('transactions.location_id', request()->location_id);

            }

            if (!empty(request()->fuel_tank_number)) {

                $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);

                $transactionWithpurchaseLine->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);

            }

            if (!empty(request()->product_id)) {

                $query->where('fuel_tanks.product_id', request()->product_id);

                $transactionWithpurchaseLine->where('fuel_tanks.product_id', request()->product_id);

            }

            if (!empty(request()->settlement_id)) {

                $query->where('transactions.invoice_no', request()->settlement_id);
                
            

                $transactionWithpurchaseLine->where('transactions.invoice_no', request()->settlement_id);

            }

            if (!empty(request()->purchase_no)) {

                $query->where('transactions.ref_no', request()->purchase_no);

                $transactionWithpurchaseLine->where('transactions.ref_no', request()->purchase_no);

            }

            $query->union($transactionWithpurchaseLine);

            $business_id = session()->get('user.business_id');

            $business_details = Business::find($business_id);

            $tanks_transaction_details = Datatables::of($query)

                ->removeColumn('id')

                ->editColumn('created_at', function ($row) {

                    return date('Y-m-d H:i:s', strtotime($row->created_at));;

                })

                ->editColumn('transaction_date', '{{date("Y-m-d", strtotime($transaction_date))}}')

                ->addColumn('balance_qty', function ($row) use ($business_details) {
                    
                    

                    $business_id = request()->session()->get('user.business_id');

                    $purchase_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                        ->join('fuel_tanks', function ($join) {

                            $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                        })

                        ->where('transactions.business_id', $business_id)

                        ->where('fuel_tanks.id', $row->fuel_tank_id)->where('transactions.created_at', '<=', $row->created_at)

                        ->select('transactions.created_at', DB::raw('SUM(tank_purchase_lines.quantity) as balance_qty'))

                        ->first()

                        ->balance_qty;

                    $sell = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                        ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                        ->join('fuel_tanks', function ($join) {

                            $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                        })

                        ->where('transactions.business_id', $business_id)

                        ->where('fuel_tanks.id', $row->fuel_tank_id)->where('transactions.created_at', '<=', $row->created_at)

                        ->select('transactions.created_at', DB::raw('SUM( tank_sell_lines.quantity) as balance_qty'))

                        ->orderBy('transactions.created_at', 'desc')

                        ->first()

                        ->balance_qty;
                       
                         
                    $balance_qty =  $purchase_qty - abs($sell);
                    
                   
                    
                    

                    return $this->productUtil->num_f($balance_qty, false, $business_details, true);

                })

                ->addColumn('opening_balance_qty', function ($row) use ($business_details) {

                    $business_id = request()->session()->get('user.business_id');

                    if ($row->type == 'opening_stock') {

                        return $this->productUtil->num_f($row->purchase_qty, false, $business_details, true);

                    } else {

                        $purchase_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                            ->join('fuel_tanks', function ($join) {

                                $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                            })

                            ->where('transactions.business_id', $business_id)

                            ->where('fuel_tanks.id', $row->fuel_tank_id)->where('transactions.created_at', '<', $row->created_at)

                            ->select('transactions.created_at', DB::raw('SUM(tank_purchase_lines.quantity) as opening_balance_qty'))

                            ->first()

                            ->opening_balance_qty;

                        $sell = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                            ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                            ->join('fuel_tanks', function ($join) {

                                $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                            })

                            ->where('transactions.business_id', $business_id)

                            ->where('fuel_tanks.id', $row->fuel_tank_id)->where('transactions.created_at', '<', $row->created_at)

                            ->select('transactions.created_at', DB::raw('SUM( tank_sell_lines.quantity) as opening_balance_qty'))

                            ->orderBy('transactions.created_at', 'desc')

                            ->first()

                            ->opening_balance_qty;
                        
                        $opening_balance_qty = $purchase_qty - abs($sell);
                        
                        
                        return $this->productUtil->num_f($opening_balance_qty, false, $business_details, true);

                    }

                })

                ->editColumn('purchase_qty', function ($row) use ($business_details) {

                    if ($row->type == 'opening_stock' || $row->type == 'sell') {

                        return '';

                    } else {

                        return $this->productUtil->num_f($row->purchase_qty, false, $business_details, true);

                    }

                })

                ->editColumn('sold_qty', function ($row) use ($business_details) {

                    if ($row->type == 'opening_stock' || $row->type == 'purchase') {

                        return '';

                    } else {

                        return $this->productUtil->num_f(abs($row->sold_qty), false, $business_details, true);

                    }

                })

                ->editColumn('ref_no', function ($row) {

                    /**

                     * @ModifiedBy Afes Oktavianus

                     * @DateBy 07-06-2021

                     * @Task 3341

                     */

                    if ($row->type == 'sell' || $row->type == 'stock_adjustment') {

                        return __('petro::lang.settlment');

                    } else if ($row->type == 'opening_stock') {

                        return __('petro::lang.opening_stock');

                    } else if ($row->type == 'purchase') {

                        return __('petro::lang.purchase_reference_no') . ': ' . $row->ref_no;

                    }

                })

                ->addColumn('purchase_order_no', function ($row) {

                    /**

                     * @ModifiedBy Afes Oktavianus

                     * @DateBy 07-06-2021

                     * @Task 3341

                     */

                    if ($row->type == 'sell' || $row->type == 'stock_adjustment') {

                        return $row->invoice_no;

                    } else {

                        return $row->ref_no;

                    }

                });

            return $tanks_transaction_details->rawColumns(['transaction_date', 'balance_qty', 'ref_no'])

                ->make(true);

        }

        return view('petro::tanks_transaction_details.index');

    }



    /**

     * Show tank transaction summary

     *

     * @return \Illuminate\Http\Response

     */

    public function tankTransactionSummary()

    {

        set_time_limit(0);

        if (request()->ajax()) {

            $this->settleTransactionSummary();

            $business_id = request()->session()->get('user.business_id');

            $business_details = Business::find($business_id);

            if (request()->ajax()) {

                $start_date = request()->start_date;

                $end_date = request()->end_date;

                $query = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                    ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                    ->join('fuel_tanks', function ($join) {

                        $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                    })

                    ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                    ->where('fuel_tanks.business_id', $business_id)

                    ->whereDate('transactions.transaction_date', '>=', $start_date)

                    ->whereDate('transactions.transaction_date', '<=', $end_date)

                    ->where('transactions.type', '!=', 'opening_stock')

                    ->select(

                        'fuel_tanks.fuel_tank_number',

                        'fuel_tanks.id as fuel_tank_id',

                        'transactions.transaction_date',

                        'transactions.created_at',

                        DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                        DB::raw("fuel_tanks.current_balance as total_stock"),

                        DB::raw("(select

                        SUM(tpl.quantity) as purchase_qty

                        from `transactions` purchase_transactions

                        left join `tank_sell_lines` purchase_transactions_sell on `purchase_transactions`.`id` = `purchase_transactions_sell`.`transaction_id`

                        left join `tank_purchase_lines` tpl on `purchase_transactions`.`id` = `tpl`.`transaction_id`

                        inner join `fuel_tanks` sell_ft on `tpl`.`tank_id` = `sell_ft`.`id`

                        where `sell_ft`.`business_id` = `fuel_tanks`.`business_id`

                        and tpl.quantity != 0

                        and date(`purchase_transactions`.`transaction_date`) >= `transactions`.`transaction_date`

                        and date(`purchase_transactions`.`transaction_date`) <= `transactions`.`transaction_date`

                        and `purchase_transactions`.`type` != 'opening_stock'

                         and `purchase_transactions`.`deleted_at` is null

                        and `sell_ft`.`id` = `fuel_tanks`.`id`

                         group by `sell_ft`.`id`, date(`purchase_transactions`.`transaction_date`)

                         order by `purchase_transactions`.`transaction_date` asc

                        ) as purchase_qty"),

                        'products.name as product_name'

                    )->orderBy('transactions.transaction_date')

                    ->groupBy('fuel_tanks.id', 'transactions.transaction_date');

                if (!empty(request()->location_id)) {

                    $query->where('transactions.location_id', request()->location_id);

                }

                if (!empty(request()->fuel_tank_number)) {

                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);

                }

                if (!empty(request()->product_id)) {

                    $query->where('fuel_tanks.product_id', request()->product_id);

                }

                $tanks_transaction_details = Datatables::of($query)

                    ->editColumn('transaction_date', function ($row) {

                        return date('Y-m-d', strtotime($row->transaction_date));;

                    })

                    ->editColumn('created_at', function ($row) {

                        return date('Y-m-d H:i:s', strtotime($row->created_at));;

                    })

                    ->addColumn('starting_qty', function ($row) use ($business_details, $business_id, $start_date, $end_date) {

                        $query = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                            ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                            ->join('fuel_tanks', function ($join) {

                                $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                            })

                            ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                            ->where('fuel_tanks.business_id', $business_id)

                            ->whereDate('transactions.transaction_date', '<=', $row->transaction_date)

                            ->whereDate('transactions.transaction_date', '>=', $row->transaction_date)

                            ->where('transactions.type', '=', 'opening_stock')

                            ->where('fuel_tanks.id', $row->fuel_tank_id)

                            ->select(

                                DB::raw('SUM(tank_purchase_lines.quantity) as starting_qty'),

                            )

                            ->orderBy('fuel_tanks.fuel_tank_number')

                            ->groupBy('fuel_tanks.id')

                            ->first();

                        if ($query) {
                            

                            $starting_qty = $query->starting_qty;

                        } else {

                            $sold_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                                ->join('fuel_tanks', function ($join) {

                                    $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                                })

                                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                                ->where('fuel_tanks.business_id', $business_id)

                                ->whereDate('transactions.transaction_date', '<', $row->transaction_date)

                                ->where('transactions.type', '!=', 'opening_stock')

                                ->where('fuel_tanks.id', $row->fuel_tank_id)

                                ->select(

                                    DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                                )->orderBy('transactions.transaction_date')

                                ->groupBy('fuel_tanks.id')

                                ->first();

                            $sold_qty = $sold_qty ? $sold_qty->sold_qty : 0;

                            $purchase_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                                ->join('fuel_tanks', function ($join) {

                                    $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                                })

                                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                                ->where('fuel_tanks.business_id', $business_id)

                                ->whereDate('transactions.transaction_date', '<', $row->transaction_date)

                                ->where('fuel_tanks.id', $row->fuel_tank_id)

                                ->select(

                                    DB::raw('SUM(tank_purchase_lines.quantity) as purchase_qty'),

                                )->orderBy('transactions.transaction_date')

                                ->groupBy('fuel_tanks.id')

                                ->first();

                            $purchase_qty = $purchase_qty ? $purchase_qty->purchase_qty : 0;

                            $starting_qty = $purchase_qty - $sold_qty;

                        }

                        return $this->productUtil->num_f($starting_qty, false, $business_details, true);

                    })



                    ->editColumn('sold_qty', '{{@format_quantity($sold_qty)}}')

                    ->editColumn('purchase_qty', '{{@format_quantity($purchase_qty)}}')

                    ->addColumn('balance_qty', function ($row) use ($business_details, $business_id, $start_date, $end_date) {

                        $query = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                            ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                            ->join('fuel_tanks', function ($join) {

                                $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                            })

                            ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                            ->where('fuel_tanks.business_id', $business_id)

                            ->whereDate('transactions.transaction_date', '<=', $row->transaction_date)

                            ->whereDate('transactions.transaction_date', '>=', $row->transaction_date)

                            ->where('transactions.type', '=', 'opening_stock')

                            ->where('fuel_tanks.id', $row->fuel_tank_id)

                            ->select(

                                DB::raw('SUM(tank_purchase_lines.quantity) as starting_qty'),

                            )

                            ->orderBy('fuel_tanks.fuel_tank_number')

                            ->groupBy('fuel_tanks.id')

                            ->first();

                        if ($query) {

                            $starting_qty = $query->starting_qty;

                        } else {

                            $sold_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                                ->join('fuel_tanks', function ($join) {

                                    $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                                })

                                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                                ->where('fuel_tanks.business_id', $business_id)

                                ->whereDate('transactions.transaction_date', '<', $row->transaction_date)

                                ->where('transactions.type', '!=', 'opening_stock')

                                ->where('fuel_tanks.id', $row->fuel_tank_id)

                                ->select(

                                    DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                                )->orderBy('transactions.transaction_date')

                                ->groupBy('fuel_tanks.id')

                                ->first();

                            $sold_qty = $sold_qty ? $sold_qty->sold_qty : 0;

                            $purchase_qty = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                                ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                                ->join('fuel_tanks', function ($join) {

                                    $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                                })

                                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                                ->where('fuel_tanks.business_id', $business_id)

                                ->whereDate('transactions.transaction_date', '<', $row->transaction_date)

                                ->where('fuel_tanks.id', $row->fuel_tank_id)

                                ->select(

                                    DB::raw('SUM(tank_purchase_lines.quantity) as purchase_qty'),

                                )->orderBy('transactions.transaction_date')

                                ->groupBy('fuel_tanks.id')

                                ->first();

                            $purchase_qty = $purchase_qty ? $purchase_qty->purchase_qty : 0;

                            $starting_qty = $purchase_qty - $sold_qty;

                        }



                        $balance = $starting_qty + $row->purchase_qty - $row->sold_qty;

                        return $this->productUtil->num_f($balance, false, $business_details, true);

                    });

                return $tanks_transaction_details->rawColumns(['balance_qty', 'opening_stock', 'total_stock', 'sold_qty', 'purchase_qty'])

                    ->make(true);

            }

        }

        return view('petro::tanks_transaction_details.tank_transactions_summary');

    }

    public function settleTransactionSummary()

    {

        $business_id = request()->session()->get('user.business_id');

        $business_details = Business::find($business_id);

        $business_location_id = BusinessLocation::where('business_id', $business_id)->get()->first()->id;

        $fuel_tanks = DB::select("SELECT * FROM `fuel_tanks` WHERE  business_id = $business_id");

        $start_date = date('Y-m-d', strtotime($business_details->start_date));

        $end_date = date('2020-12-31');

        while (strtotime($start_date) <= strtotime($end_date)) {

            foreach ($fuel_tanks as $fuel_tank_id) {

                $query = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                    ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                    ->join('fuel_tanks', function ($join) {

                        $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                    })

                    ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                    ->where('fuel_tanks.business_id', $business_id)

                    ->Where('fuel_tanks.id', $fuel_tank_id->id)

                    ->whereDate('transactions.transaction_date', '>=', $start_date)

                    ->whereDate('transactions.transaction_date', '<=', $start_date)

                    ->where('transactions.type', '!=', 'opening_stock')

                    ->select(

                        'fuel_tanks.fuel_tank_number',

                        'fuel_tanks.id as fuel_tank_id',

                        'transactions.transaction_date',

                        'transactions.created_at',

                        DB::raw('SUM(tank_sell_lines.quantity) as sell_qty'),

                        DB::raw("fuel_tanks.current_balance as total_stock"),

                        'products.name as product_name'

                    )->orderBy('transactions.transaction_date')

                    ->groupBy('fuel_tanks.id', 'transactions.transaction_date');

                if (!($query->count() > 0)) {

                    $transaction = new Transaction();

                    $transaction->business_id = $business_id;

                    $transaction->location_id = $business_location_id;

                    $transaction->type = 'sell';

                    $transaction->status = 'final';

                    $transaction->transaction_date = $start_date;

                    $transaction->created_by = 2;

                    $transaction->save();

                    $transaction_id = $transaction->id;

                    DB::insert('insert into tank_sell_lines (business_id, transaction_id,tank_id,product_id,quantity) values (?, ?,?, ?,?)', [$business_id, $transaction_id, $fuel_tank_id->id, $fuel_tank_id->product_id, '0.00000']);

                    echo $start_date;

                }

            }

            $start_date = strtotime("1 day", strtotime($start_date));

            $start_date = date('Y-m-d', $start_date);

        }

    }

}

