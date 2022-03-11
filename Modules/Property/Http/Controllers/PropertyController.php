<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Property\Entities\FinanceOption;
use Modules\Property\Entities\InstallmentCycle;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyBlock;
use Yajra\DataTables\Facades\DataTables;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       
        if (!auth()->user()->can('property.list.view') && !auth()->user()->can('property.list.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Property::leftJoin('transactions', 'properties.transaction_id', '=', 'transactions.id')
                ->leftJoin('units', 'properties.unit_id', '=', 'units.id')
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
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
                ->leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')

                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('property_account_settings', 'properties.id', '=', 'property_account_settings.property_id')
                ->leftJoin('transactions as expense', 'properties.id', '=', 'expense.property_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'property_purchase')
                ->select(
                    'properties.id as property_id',
                    'properties.name as property_name',
                    'properties.extent',
                    'properties.status as property_status',
                    'units.actual_name',
                    'transactions.id',
                    'transactions.deed_no',
                    'transactions.document',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'contacts.name as supplier_name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'BS.name as location_name',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'TP.method',
                    'TP.account_id',
                    'expense.id as expense_transaction_id',
                    'property_account_settings.id as account_settings_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )->groupBy('properties.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (!empty(request()->input('property_id'))) {
                $purchases->where('properties.id', request()->input('property_id'));
            }

            if (!empty(request()->status)) {
                $purchases->where('properties.status', request()->status);
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

                    if (auth()->user()->can("property.list.edit")) {
                        $html .= '<li><a href="' . action('\Modules\Property\Http\Controllers\PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                    }
                    $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyBlocksController@create', ['id' => $row->property_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-th" aria-hidden="true"></i>' . __("property::lang.add_block") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyBlocksController@edit', $row->property_id) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("property::lang.edit_block") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyBlocksController@show', [$row->property_id]) . '" class="btn-modal" data-container=".block_modal"><i class="fa fa-search-plus" aria-hidden="true"></i>' . __("property::lang.block_details") . '</a></li>';
                    $html .= '<li><a href="' . action('\Modules\Property\Http\Controllers\PropertyBlocksController@getImport', [$row->property_id]) . '" class=""><i class="fa fa-download" aria-hidden="true"></i>' . __("property::lang.import_blocks") . '</a></li>';
                    $html .= '<li><a href="' . action('\Modules\Property\Http\Controllers\ExpenseController@create', ['property_id' => $row->property_id]) . '" class=""><i class="fa fa-minus-circle" aria-hidden="true"></i>' . __("property::lang.expenses") . '</a></li>';
                    if(!empty($row->expense_transaction_id)){
                        $html .= '<li><a href="' . action('\Modules\Property\Http\Controllers\ExpenseController@edit', [$row->expense_transaction_id]) . '" class=""><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("property::lang.edit_expenses") . '</a></li>';
                        $html .= '<li><a href="" data-href="' . action('\Modules\Property\Http\Controllers\ExpenseController@show' , [$row->expense_transaction_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("Expense Detail") . '</a></li>';
                    }
                    $html .= '<li class="divider"></li>';
                    if (empty($row->account_settings_id)) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyAccountSettingController@create', ['property_id' => $row->property_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-cogs" aria-hidden="true"></i>' . __("property::lang.account_settings") . '</a></li>';
                    } else {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyAccountSettingController@show', $row->account_settings_id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("property::lang.view_account_settings") . '</a></li>';
                        if (auth()->user()->can('property_account_settings.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyAccountSettingController@edit', $row->account_settings_id) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("property::lang.edit_account_settings") . '</a></li>';
                        }
                    }


                    $html .=  '</ul></div>';
                    return $html;
                })
                ->addColumn('purchase_no', function ($row) {
                    return $row->id;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->invoice_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') . '"><i class="fa fa-undo"></i></small>' : $row->ref_no;
                })
                ->addColumn('no_of_blocks', function ($row) {
                    $block_count = PropertyBlock::where('property_id', $row->property_id)->count();
                    return  $block_count;
                })
                ->editColumn('added_by', function ($row) {
                    $block_count = PropertyBlock::where('property_id', $row->property_id)->first();
                    if(!empty($block_count )){
                        $user = User::where('id', $block_count->added_by)->first();
                        if (!empty($user)) {
                            return $user->first_name . ' ' . $user->last_name;
                        }
                    }
                    return '';
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('property_status', '{{ucfirst($property_status)}}')
                ->editColumn('extent', '{{@format_quantity($extent)}}')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('pay_terms', '{{$pay_term_number}} {{ucfirst($pay_term_type)}}')
                ->editColumn(
                    'status',
                    '<a href="#" @if(auth()->user()->can("property.list.edit")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )

                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') . ':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') . ':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal no-print"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a><span class="display_currency print_section" data-currency_symbol="true">' . $return_due . '</span>';
                    }
                    return $due_html;
                })
                ->addColumn('payment_method', function ($row) {
                    $html = '';
                    if ($row->method == 'bank_transfer') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else {
                        $html .= ucfirst($row->method);
                    }

                    return $html;
                })

                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no', 'payment_method'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::propertyCustomerDropdown($business_id, false, true);
        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');
        $blocks = Property::getLandAndBlockDropdown($business_id, true, true);
        $properties = Property::where('business_id', $business_id)->pluck('name', 'id');
        $statuses = Property::statusesDropdown();
        $installment_cycles  = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::properties.index')
            ->with(compact(
                'business_locations',
                'customers',
                'suppliers',
                'finance_options',
                'blocks',
                'properties',
                'customers',
                'installment_cycles',
                'statuses'
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
    public function getCustomerDetails($id)
    {
        $contact = Contact::find($id);

        return view('property::properties.partials.customer_details')->with(compact(
            'contact'
        ));
    }
    public function getPropertyDetails($id)
    {
        // $property = Property::where('properties.id', $id)
        //     ->select('property_blocks.*')->first();
        $property = Property::leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')->where('properties.id', $id)
            ->select('property_blocks.*')->first();

        return $property;
    }
}
