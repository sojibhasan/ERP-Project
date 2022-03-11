<?php
namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\CustomerReference;
use App\Product;
use App\Utils\ProductUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\DailyVoucherItem;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use Yajra\DataTables\Facades\DataTables;

class DailyVoucherController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('daily_voucher.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $daily_vouchers = DailyVoucher::leftjoin('pumps', 'daily_vouchers.pump_id', 'pumps.id')
                ->leftjoin('pump_operators', 'daily_vouchers.operator_id', 'pump_operators.id')
                ->leftjoin('contacts', 'daily_vouchers.customer_id', 'contacts.id')
                ->leftjoin('business_locations', 'daily_vouchers.location_id', 'business_locations.id')
                ->leftjoin('customer_references', 'daily_vouchers.vehicle_no', 'customer_references.id')
                ->leftjoin('users', 'daily_vouchers.created_by', 'users.id')
                ->where('daily_vouchers.business_id', $business_id)
                ->select(
                    'daily_vouchers.*',
                    'pumps.pump_name',
                    'business_locations.name as location_name',
                    'pump_operators.name as operator_name',
                    'customer_references.reference',
                    'contacts.name as customer_name',
                    'users.username as username'
                );
            if(!empty(request()->location_id)){
                $daily_vouchers->where('daily_vouchers.location_id', request()->location_id);
            }
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $daily_vouchers->whereDate('daily_vouchers.transaction_date', '>=',request()->start_date);
                $daily_vouchers->whereDate('daily_vouchers.transaction_date', '<=',request()->end_date);
            }

            return DataTables::of($daily_vouchers)

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('daily_voucher.view')) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\DailyVoucherController@print', $row->id) . '" class="print_bill" ><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('daily_voucher.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;
        $busness_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        return view('petro::daily_collection.partials.create_daily_voucher')->with(compact(
            'customers',
            'pumps',
            'pump_operators',
            'daily_vouchers_no',
            'busness_locations',
            'products'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            $data = array(
                'business_id' => $business_id,
                'transaction_date' => Carbon::parse($request->transaction_date)->format('Y-m-d'),
                'daily_vouchers_no' => $request->daily_vouchers_no,
                'location_id' => $request->location_id,
                'pump_id' => $request->pump_id,
                'operator_id' => $request->operator_id,
                'customer_id' => $request->customer_id,
                'current_outstanding' => $request->current_outstanding,
                'outstanding_pending' => $request->outstanding_pending,
                'voucher_order_number' => $request->voucher_order_number,
                'voucher_order_date' => Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                'vehicle_no' => $request->vehicle_no,
                'status' => 1,
                'created_by' => Auth::user()->id
            );
            DB::beginTransaction();
            $daily_voucher = DailyVoucher::create($data);

            $total_amount = 0;
            foreach ($request->daily_voucher as $bill_detail) {
                $total_amount += $bill_detail['sub_total'];
                $details = array(
                    'business_id' => $business_id,
                    'daily_voucher_id' => $daily_voucher->id,
                    'product_id' => $bill_detail['product_id'],
                    'unit_price' => $bill_detail['unit_price'],
                    'qty' => $bill_detail['qty'],
                    'sub_total' => $bill_detail['sub_total'],

                );
                DailyVoucherItem::create($details);
            }

            $daily_voucher->total_amount = $total_amount;

            $daily_voucher->save();

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('petro::lang.daily_voucher_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCustomerReference($id)
    {
        $refs = CustomerReference::where('contact_id', $id)->select('reference', 'id')->get();

        $html = '<option>Please Select</option>';

        foreach ($refs as $ref) {
            $html .= '<option value="' . $ref->id . '">' . $ref->reference . '</option>';
        }

        return $html;
    }

    public function getProductPrice($id)
    {
        $price = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('variations.product_id', $id)
            ->select('default_sell_price')
            ->first();

        if (!empty($price)) {
            return $this->productUtil->num_f($price->default_sell_price);
        } else {
            return '0';
        }
    }

    public function getProductRow()
    {
        $index = request()->index;
        $business_id = request()->session()->get('business.id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        return view('petro::daily_collection.partials.product_row')->with(compact('products', 'index'));
    }

    public function print($id)
    {
        $daily_voucher = DailyVoucher::leftjoin('pumps', 'daily_vouchers.pump_id', 'pumps.id')
            ->leftjoin('pump_operators', 'daily_vouchers.operator_id', 'pump_operators.id')
            ->leftjoin('contacts', 'daily_vouchers.customer_id', 'contacts.id')
            ->leftjoin('customer_references', 'daily_vouchers.vehicle_no', 'customer_references.id')
            ->leftjoin('users', 'daily_vouchers.created_by', 'users.id')
            ->where('daily_vouchers.id', $id)
            ->select(
                'daily_vouchers.*',
                'pumps.pump_name',
                'pump_operators.name as operator_name',
                'customer_references.reference',
                'contacts.name as customer_name',
                'users.username as username'
            )->first();

        $daily_voucher_items = DailyVoucherItem::leftjoin('products', 'daily_voucher_items.product_id', 'products.id')
            ->where('daily_voucher_items.daily_voucher_id', $id)
            ->select('daily_voucher_items.*', 'products.name as product_name')
            ->get();

        return view('petro::daily_collection.partials.print_daily_voucher')->with(compact('daily_voucher', 'daily_voucher_items'));
    }
}
