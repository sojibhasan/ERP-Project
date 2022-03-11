<?php
namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\CustomerReference;
use Illuminate\Routing\Controller;
use App\Product;
use Illuminate\Http\Request;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\ProductUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\DailyVoucherItem;
use Modules\Petro\Entities\IssueCustomerBill;
use Modules\Petro\Entities\IssueCustomerBillDetail;
use Yajra\DataTables\Facades\DataTables;

class IssueCustomerBillController extends Controller
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
        if (!auth()->user()->can('issue_customer_bill.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $issue_customer_bills = IssueCustomerBill::leftjoin('pumps', 'issue_customer_bills.pump_id', 'pumps.id')
                ->leftjoin('pump_operators', 'issue_customer_bills.operator_id', 'pump_operators.id')
                ->leftjoin('contacts', 'issue_customer_bills.customer_id', 'contacts.id')
                ->leftjoin('customer_references', 'issue_customer_bills.reference_id', 'customer_references.id')
                ->leftjoin('users', 'issue_customer_bills.created_by', 'users.id')
                ->where('issue_customer_bills.business_id', $business_id)
                ->select(
                    'issue_customer_bills.*',
                    'pumps.pump_name',
                    'pump_operators.name as operator_name',
                    'customer_references.reference',
                    'contacts.name as customer_name',
                    'users.username as username'
                );

            return DataTables::of($issue_customer_bills)

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('issue_customer_bill.view')) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\IssueCustomerBillController@print', $row->id) . '" class="print_bill" ><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('petro::issue_bill_customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('issue_customer_bill.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $customer_bill_no = (IssueCustomerBill::where('business_id', $business_id)->count()) + 1;
        $busness_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        return view('petro::issue_bill_customer.create')->with(compact(
            'customers',
            'pumps',
            'pump_operators',
            'customer_bill_no',
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
        // try {
            $business_id = request()->session()->get('business.id');

            $data = array(
                'business_id' => $business_id,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'customer_bill_no' => $request->customer_bill_no,
                'location_id' => $request->location_id,
                'pump_id' => $request->pump_id,
                'operator_id' => $request->operator_id,
                'customer_id' => $request->customer_id,
                'reference_id' => $request->reference_id,
                'order_bill_no' => $request->order_bill_no,
                'show_in_daily_voucher' => $request->show_in_daily_voucher,
                'created_by' => Auth::user()->id
            );
            DB::beginTransaction();
            $issue_customer_bill = IssueCustomerBill::create($data);

            $total_amount = 0;
            foreach ($request->issue_customer_bill as $bill_detail) {
                $total_amount += $bill_detail['sub_total'];
                $details = array(
                    'business_id' => $business_id,
                    'issue_bill_id' => $issue_customer_bill->id,
                    'product_id' => $bill_detail['product_id'],
                    'unit_price' => $bill_detail['unit_price'],
                    'qty' => $bill_detail['qty'],
                    'discount' => $bill_detail['discount'],
                    'tax' => $bill_detail['tax'],
                    'sub_total' => $bill_detail['sub_total'],

                );
                IssueCustomerBillDetail::create($details);
            }

            $issue_customer_bill->total_amount = $total_amount;

            $issue_customer_bill->save();

            if($request->show_in_daily_voucher == 1){
                $customer_details = app('App\Http\Controllers\SellPosController')->getCustomerDetails($request);
                $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;

                $data = array(
                    'business_id' => $business_id,
                    'transaction_date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'daily_vouchers_no' => $daily_vouchers_no,
                    'location_id' => $request->location_id,
                    'pump_id' => $request->pump_id,
                    'operator_id' => $request->operator_id,
                    'customer_id' => $request->customer_id,
                    'voucher_order_number' => $request->voucher_order_number,
                    'voucher_order_date' => Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                    'vehicle_no' => $request->reference_id,
                    'current_outstanding' => $customer_details['due_amount'],
                    'outstanding_pending' => $customer_details['due_amount'],
                    'total_amount' => $total_amount,
                    'is_issue_customer_bill' => 1,
                    'status' => 1,
                    'created_by' => Auth::user()->id
                );
                $daily_voucher = DailyVoucher::create($data);
    
                foreach ($request->issue_customer_bill as $bill_detail) {
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
    
            }

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('petro::lang.issue_customer_bill_create_success')
            ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('messages.something_went_wrong')
        //     ];
        // }


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
        return view('petro::issue_bill_customer.partials.product_row')->with(compact('products', 'index'));
    }

    public function print($id)
    {
        $issue_customer_bill = IssueCustomerBill::leftjoin('pumps', 'issue_customer_bills.pump_id', 'pumps.id')
            ->leftjoin('pump_operators', 'issue_customer_bills.operator_id', 'pump_operators.id')
            ->leftjoin('contacts', 'issue_customer_bills.customer_id', 'contacts.id')
            ->leftjoin('customer_references', 'issue_customer_bills.reference_id', 'customer_references.id')
            ->leftjoin('users', 'issue_customer_bills.created_by', 'users.id')
            ->where('issue_customer_bills.id', $id)
            ->select(
                'issue_customer_bills.*',
                'pumps.pump_name',
                'pump_operators.name as operator_name',
                'customer_references.reference',
                'contacts.name as customer_name',
                'users.username as username'
            )->first();

        $bill_details = IssueCustomerBillDetail::leftjoin('products', 'issue_customer_bill_details.product_id', 'products.id')
            ->where('issue_customer_bill_details.issue_bill_id', $id)
            ->select('issue_customer_bill_details.*', 'products.name as product_name')
            ->get();

        return view('petro::issue_bill_customer.print')->with(compact('issue_customer_bill', 'bill_details'));
    }
}
