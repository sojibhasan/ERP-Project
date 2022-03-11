<?php

namespace Modules\Property\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Property\Entities\Property;
use phpDocumentor\Reflection\Types\Boolean;
use Yajra\DataTables\Facades\DataTables;

class EasyPaymentController extends Controller
{
    protected $commonUtil;

    /**
     * Constructor
     *
     *
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('list_easy_payments.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {

            $purchases = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
                ->leftJoin('properties', 'property_sell_lines.property_id', '=', 'properties.id')
                ->leftJoin('property_blocks', 'property_sell_lines.block_id', '=', 'property_blocks.id')
                ->leftJoin('units', 'properties.unit_id', '=', 'units.id')
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('property_finalizes', 'property_sell_lines.id', '=', 'property_finalizes.property_sell_line_id')
                ->leftJoin('installment_cycles', 'property_finalizes.installment_cycle_id', '=', 'installment_cycles.id')
                ->leftJoin('finance_options', 'property_finalizes.finance_option_id', '=', 'finance_options.id')
                ->leftJoin(
                    'business_locations AS BS',
                    'transactions.location_id',
                    '=',
                    'BS.id'
                )
                ->leftJoin(
                    'transaction_payments AS TP',
                    'transactions.id',
                    '=',
                    'TP.transaction_id'
                )
                ->leftJoin(
                    'penalties',
                    'property_sell_lines.id',
                    '=',
                    'penalties.sell_line_id'
                )

                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'property_sell')
                ->select(
                    'properties.id as property_id',
                    'properties.name as property_name',
                    'properties.extent',
                    'property_blocks.id as block_id',
                    'property_blocks.block_number',
                    'property_blocks.block_extent',
                    'property_blocks.block_sale_price',
                    'property_blocks.block_sold_price',
                    'property_blocks.is_finalized',
                    'property_finalizes.id as finalize_id',
                    'property_finalizes.down_payment',
                    'property_finalizes.installment_amount',
                    'property_finalizes.loan_capital',
                    'property_finalizes.total_interest',
                    'property_finalizes.no_of_installment',
                    'property_finalizes.easy_payment',
                    'property_finalizes.first_installment_date',
                    'transactions.transaction_date',
                    'contacts.name as customer_name',
                    'transactions.final_total',
                    'installment_cycles.name as installment_cycle',
                    'BS.name as location_name',
                    'TP.paid_on',
                    'property_sell_lines.id as psl_id',
                    'finance_options.finance_option',
                    'penalties.id as penalty_id',
                    DB::raw('SUM(penalties.amount) as penalty'),
                    DB::raw('SUM(TP.amount) as paid_amount'),
                    DB::raw('SUM(final_total-TP.amount) as balance_due'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )->groupBy('property_sell_lines.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->project_id)) {
                $purchases->where('properties.id', request()->project_id);
            }
            if (!empty(request()->customer_id)) {
                $purchases->where('contacts.id', request()->customer_id);
            }
            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            $show_only_penalty = request()->show_only_penalty;
            if ($show_only_penalty == 'true') {
                $purchases->whereNotNull('penalties.id');
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }
            return DataTables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PenaltyController@create', ['sell_line_id' => $row->psl_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-arrow-down" aria-hidden="true"></i>' . __("property::lang.add_penalty") . '</a></li>';

                    if (!empty($row->penalty_id)) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PenaltyController@show', $row->psl_id) . '?show_delete=0" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("property::lang.show_penalty") . '</a></li>';

                        if (auth()->user()->can('property_penalty.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PenaltyController@show', $row->psl_id) . '?show_delete=1" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i>' . __("property::lang.delete_penalty") . '</a></li>';
                        }
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->addColumn('purchase_no', function ($row) {
                    return $row->id;
                })
                ->addColumn('reservation_amount', function ($row) {
                    return 0;
                })
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('first_installment_date', '@if(!empty($first_installment_date)){{@format_date($first_installment_date)}}@endif')

                ->editColumn(
                    'installment_amount',
                    '<span class="display_currency installment_amount" data-currency_symbol="false" data-orig-value="{{$installment_amount}}">{{$installment_amount}}</span>'
                )
                ->editColumn(
                    'down_payment',
                    '<span class="display_currency down_payment" data-currency_symbol="false" data-orig-value="{{$down_payment}}">{{$down_payment}}</span>'
                )
                ->editColumn(
                    'installment_amount',
                    '<span class="display_currency installment_amount" data-currency_symbol="false" data-orig-value="{{$installment_amount}}">{{$installment_amount}}</span>'
                )
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="false" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'loan_capital',
                    '<span class="display_currency loan_capital" data-currency_symbol="false" data-orig-value="{{$loan_capital}}">{{$loan_capital}}</span>'
                )
                ->editColumn(
                    'total_interest',
                    '<span class="display_currency total_interest" data-currency_symbol="false" data-orig-value="{{$total_interest}}">{{$total_interest}}</span>'
                )
                ->editColumn(
                    'paid_amount',
                    '<span class="display_currency paid_amount" data-currency_symbol="false" data-orig-value="{{$paid_amount}}">{{$paid_amount}}</span>'
                )
                ->addColumn(
                    'penalty',
                    '<span class="display_currency penalty" data-currency_symbol="false" data-orig-value="{{$penalty}}">{{$penalty}}</span>'
                )
                ->addColumn(
                    'balance_due',
                    '<span class="display_currency balance_due" data-currency_symbol="false" data-orig-value="{{$final_total- $paid_amount}}">{{$final_total- $paid_amount}}</span>'
                )
                ->addColumn(
                    'due',
                    '{{$final_total- $paid_amount}}'
                )

                ->removeColumn('id')
                ->rawColumns([
                    'final_total',
                    'action',
                    'payment_due',
                    'installment_amount',
                    'capital',
                    'final_total',
                    'loan_capital',
                    'total_interest',
                    'paid_amount',
                    'penalty',
                    'balance_due'
                ])
                ->make(true);
        }

        $customers = Contact::propertyCustomerDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $projects = Property::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::easy_payments.index')->with(compact(
            'customers',
            'business_locations',
            'projects'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('property::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('property::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('property::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }



    public function getAgingReport(Request $request)
    {
        if (!auth()->user()->can('aging_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        if (request()->ajax()) {

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftjoin('installments', 'transactions.id', 'installments.transaction_id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'property_sell')
                ->where('installments.payment_status', 'due')
                ->whereIn('transactions.payment_status', ['due', 'partial'])
                ->select(
                    'transactions.id',
                    'installments.amount as due_amount',
                    'installments.date as due_date',
                    'contacts.name',
                    'transactions.final_total',
                    'transactions.transaction_date',
                    DB::raw('(SELECT DATEDIFF(NOW(), installments.date)) as days_over'),
                    DB::raw('SUM(IF(tp.is_return = 1,-1*tp.amount,tp.amount)) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id'
                );

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $sells->whereDate('installments.date', '>=', request()->start_date)
                    ->whereDate('installments.date', '<=', request()->end_date);
            }

            $sells->groupBy('installments.id')->OrderBY('installments.date', 'desc');
            $business_details = Business::find($business_id);

            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        }
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> ' . __("messages.print") . '</a></li>';


                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('due_amount', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency final-total-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    return $due_amount;
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->commonUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;
                })

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                ->addColumn('1_30_days', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency 1_30_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 1 && $row->days_over <= 30) {
                        return $due_amount;
                    }
                    return null;
                })
                ->addColumn('31_45_days', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency 31_45_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 31 && $row->days_over <= 45) {
                        return $due_amount;
                    }
                    return null;
                })
                ->addColumn('46_60_days', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency 46_60_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 46 && $row->days_over <= 60) {
                        return $due_amount;
                    }
                    return null;
                })
                ->addColumn('61_90_days', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency 61_90_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 61 && $row->days_over <= 90) {
                        return $due_amount;
                    }
                    return null;
                })
                ->addColumn('over_90_days', function ($row) use ($business_details) {
                    $final = $row->due_amount ;
                    $due_amount = '<span class="display_currency over_90_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->commonUtil->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 91) {
                        return $due_amount;
                    }
                    return null;
                })
                ->editColumn('due_date', '{{@format_date($due_date)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);

            $rawColumns = [
                'days_over',
                'due_date',
                'due_amount',
                'action',
                '1_30_days',
                '31_45_days',
                '46_60_days',
                '61_90_days',
                'over_90_days'
            ];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
    }
}
