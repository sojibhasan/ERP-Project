<?php

namespace Modules\MPCS\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\FormF22Detail;
use Modules\MPCS\Entities\FormF22Header;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\VariationLocationDetails;

class F22FormController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }


    public function F22StockTaking()
    {
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $f22_counts  = FormF22Header::where('business_id', $business_id)->count();
        if (!empty($settings)) {
            $F22_from_no = $settings->F22_form_sn +  $f22_counts;
        } else {
            $F22_from_no = 1 +  $f22_counts;
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();

        $last_form = FormF22Header::where('business_id', $business_id)->orderBy('id', 'desc')->first();
        $last_form_no = !empty($last_form) ? $last_form->form_no : '';

        return view('mpcs::forms.F22.F22_stock_taking')->with(compact('F22_from_no', 'business_locations', 'products', 'settings', 'last_form_no'));
    }

    public function getF22FormList(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $header = FormF22Header::leftjoin('business_locations', 'form_f22_headers.location_id', 'business_locations.id')
                ->leftjoin('users', 'form_f22_headers.created_by', 'users.id')
                ->where('form_f22_headers.business_id', $business_id)
                ->select('form_f22_headers.*', 'business_locations.name as locations_name', 'users.username');


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $header->whereIn('form_f22_headers.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $header->where('form_f22_headers.location_id', request()->location_id);
            }


            return Datatables::of($header)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can("edit_f22_stock_Taking_form")) {
                        $html .= '<li><a href="' . action('\Modules\MPCS\Http\Controllers\F22FormController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                    }

                    $html .= '<li><a href="#" class="reprint_form" data-href="' . action('\Modules\MPCS\Http\Controllers\F22FormController@printF22FormById', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';

                    $html .= '</ul>';
                    return $html;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $form = FormF22Header::leftjoin('form_f22_details', 'form_f22_headers.id', 'form_f22_details.header_id')
                ->where('form_f22_headers.id', $id)
                ->select('form_f22_headers.*', 'form_f22_details.*', 'form_f22_details.id as detial_id');
            return DataTables::of($form)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->detial_id . '][current_stock]" id="f22[' . $row->detial_id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"  class="unit_purchase_price" name="f22[' . $row->detial_id . '][unit_purchase_price]" >';
                })
                ->editColumn('total_purchase_price', function ($row) {
                    return '<span class="display_currency total_purchase_price" data-orig-value="' . $row->purchase_price_total . '" data-currency_symbol = "false"></span><input value="' . $row->purchase_price_total . '" type="hidden" class="total_purhcase_value" name="f22[' . $row->detial_id . '][total_purhcase_value]" >';
                })
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $row->unit_sale_price . '" data-currency_symbol = "false">' . $row->unit_sale_price . '</span><input type="hidden" value="' . $row->unit_sale_price . '"   class="unit_sale_price" name="f22[' . $row->detial_id . '][unit_sale_price]" >';
                })
                ->editColumn('total_sale_price', function ($row) {
                    return '<span class="display_currency total_sale_price" data-orig-value="' . $row->sales_price_total . '" data-currency_symbol = "false"></span><input value="' . $row->sales_price_total . '" type="hidden" class="total_sale_value" name="f22[' . $row->detial_id . '][total_sale_value]" >';
                })

                ->addColumn('book_no', function ($row) {
                    return '<input class="form-control input_number book_no" name="f22[' . $row->detial_id . '][book_no]" id="f22[' . $row->detial_id . '][book_no]" style="width: 80px;" name="book_no" value="' . $row->book_no . '" >';
                })
                ->addColumn('stock_count', function ($row) {
                    return '<input class="form-control input_number stock_count"  name="f22[' . $row->detial_id . '][stock_count]" id="f22[' . $row->detial_id . '][stock_count]"  style="width: 80px;" name="stock_count" value="' . $row->stock_count . '" >';
                })
                ->addColumn('qty_difference', function ($row) {
                    return '<input class="form-control input_number qty_difference" name="f22[' . $row->detial_id . '][qty_difference]" id="f22[' . $row->detial_id . '][qty_difference]" style="width: 80px;" name="qty_difference" value="' . $row->difference_qty . '" readonly >';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->product_code . '" name="f22[' . $row->detial_id . '][sku]" id="f22[' . $row->detial_id . '][sku]" > ' . $row->sku;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->detial_id . '][product]" id="f22[' . $row->detial_id . '][product]" > ' . $row->product;
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }

        return view('mpcs::forms.F22.edit')->with(compact('id'));
    }

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            DB::beginTransaction();

            foreach ($data['f22'] as $key => $item) {
                $data_details = array(
                    'product_code' => $item['sku'],
                    'product' => $item['product'],
                    'book_no' => $item['book_no'],
                    'current_stock' => $item['current_stock'],
                    'stock_count' => $item['stock_count'],
                    'unit_purchase_price' => $item['unit_purchase_price'],
                    'unit_sale_price' => $item['unit_sale_price'],
                    'purchase_price_total' => $item['total_purhcase_value'],
                    'sales_price_total' => $item['total_sale_value'],
                    'difference_qty' => $item['qty_difference']
                );

                $details = FormF22Detail::where('id', $key)->update($data_details);
            }
            DB::commit();

            return $this->printF22FormById($id);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }

    public function getF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Product::leftjoin('purchase_lines', 'products.id', 'purchase_lines.product_id')
                ->leftjoin('transactions', 'purchase_lines.transaction_id', 'transactions.id')
                ->join(
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
                    'transactions AS PR',
                    'transactions.id',
                    '=',
                    'PR.return_parent_id'
                )
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('products.business_id', $business_id)
                ->select(
                    'transactions.id',
                    'transactions.ref_no as reference_no',
                    'purchase_lines.purchase_price as unit_purchase_price',
                    'transactions.final_total as total_purchase_price',
                    'BS.name as location',
                    'products.name as product',
                    'products.id as product_id',
                    'variations.id as variation_id',
                    'products.sku',
                    'variation_location_details.qty_available as current_stock',
                    'variations.default_sell_price',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'PR.id as return_transaction_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                    DB::raw('COUNT(PR.id) as return_exists'),
                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )
                ->groupBy('products.sku');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->product_id)) {
                $purchases->where('products.id', request()->product_id);
            }

            return DataTables::of($purchases)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->product_id . '][current_stock]" id="f22[' . $row->product_id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"  class="unit_purchase_price" name="f22[' . $row->product_id . '][unit_purchase_price]" >';
                })
                ->editColumn('total_purchase_price', function ($row) {
                    return '<span class="display_currency total_purchase_price" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" class="total_purhcase_value" name="f22[' . $row->product_id . '][total_purhcase_value]" >';
                })
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $row->default_sell_price . '" data-currency_symbol = "false">' . $row->default_sell_price . '</span><input type="hidden" value="' . $row->default_sell_price . '"   class="unit_sale_price" name="f22[' . $row->product_id . '][unit_sale_price]" >';
                })
                ->editColumn('total_sale_price', function ($row) {
                    return '<span class="display_currency total_sale_price" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" class="total_sale_value" name="f22[' . $row->product_id . '][total_sale_value]" >';
                })

                ->addColumn('book_no', function ($row) {
                    return '<input class="form-control book_no" name="f22[' . $row->product_id . '][book_no]" id="f22[' . $row->product_id . '][book_no]" style="width: 80px;" name="book_no" value="" >';
                })
                ->addColumn('stock_count', function ($row) {
                    return '<input class="form-control stock_count"  name="f22[' . $row->product_id . '][stock_count]" id="f22[' . $row->product_id . '][stock_count]"  style="width: 80px;" name="stock_count" value="" >';
                })
                ->addColumn('qty_difference', function ($row) {
                    return '<input class="form-control qty_difference" name="f22[' . $row->product_id . '][qty_difference]" id="f22[' . $row->product_id . '][qty_difference]" style="width: 80px;" name="qty_difference" value="" readonly >';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->sku . '" name="f22[' . $row->product_id . '][sku]" id="f22[' . $row->product_id . '][sku]" > ' . $row->sku;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->product_id . '][product]" id="f22[' . $row->product_id . '][product]" > ' . $row->product . ' <input type="hidden" value="' . $row->variation_id . '" name="f22[' . $row->product_id . '][variation_id]" id="f22[' . $row->product_id . '][variation_id]" >';
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }
    }

    public function getLastVerifiedF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $last_form = FormF22Header::where('business_id', $business_id)->orderBy('id', 'desc')->first();
            if (!empty($last_form)) {
                $last_form_header_id =  $last_form->id;
            } else {
                $last_form_header_id =  0;
            }
            $verified_form = FormF22Detail::where('header_id', $last_form_header_id);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $verified_form->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $verified_form->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->product_id)) {
                $verified_form->where('products.id', request()->product_id);
            }
            $index = 0;

            return Datatables::of($verified_form)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->id . '][current_stock]" id="f22[' . $row->id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"  class="unit_purchase_price" name="f22[' . $row->id . '][unit_purchase_price]" >';
                })
                ->editColumn('total_purchase_price', function ($row) {
                    return '<span class="display_currency lf_total_purchase_price" data-orig-value="' . $row->purchase_price_total . '" data-currency_symbol = "false"></span><input type="hidden"  class="total_purhcase_value" name="f22[' . $row->id . '][total_purhcase_value]" value="' . $row->purchase_price_total . '" >' . $row->purchase_price_total;
                })
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"   class="unit_sale_price" name="f22[' . $row->id . '][unit_sale_price]" >';
                })
                ->editColumn('total_sale_price', function ($row) {
                    return '<span class="display_currency lf_total_sale_price" data-orig-value="' . $row->sales_price_total . '" data-currency_symbol = "false"></span><input type="hidden" class="total_sale_value" name="f22[' . $row->id . '][total_sale_value]" value="' . $row->sales_price_total . '">' . $row->sales_price_total;
                })
                ->addColumn('book_no', function ($row) {
                    return '<span>' . $row->book_no . '</span><input type="hidden" class="form-control book_no" name="f22[' . $row->id . '][book_no]" id="f22[' . $row->id . '][book_no]"  value="' . $row->book_no . '" >';
                })
                ->addColumn('stock_count', function ($row) {
                    return '<span>' . $row->stock_count . '</span><input type="hidden" class="form-control stock_count"  name="f22[' . $row->id . '][stock_count]" id="f22[' . $row->id . '][stock_count]"  value="' . $row->stock_count . '" >';
                })
                ->addColumn('qty_difference', function ($row) {
                    return '<span>' . $row->difference_qty . '</span><input type="hidden"  class="form-control difference_qty" name="f22[' . $row->id . '][difference_qty]" id="f22[' . $row->id . '][difference_qty]" value="' . $row->difference_qty . '">';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->product_code . '" name="f22[' . $row->id . '][sku]" id="f22[' . $row->id . '][sku]" > ' . $row->product_code;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->id . '][product]" id="f22[' . $row->id . '][product]" > ' . $row->product;
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }
    }

    public function printF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $data = array();
        parse_str($request->data, $data); // converting serielize string to array
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();
        $details = $data;
        $data = $data['f22'];
        return view('mpcs::forms.F22.partials.print_f22_form')->with(compact('data', 'settings', 'details'));
    }

    public function printF22FormById($header_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $header = FormF22Header::leftjoin('business_locations', 'form_f22_headers.location_id', 'business_locations.id')
            ->where('form_f22_headers.id', $header_id)->select('business_locations.name as location_name', 'form_f22_headers.*')->first();
        $details = FormF22Detail::where('header_id', $header_id)->get();
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();

        return view('mpcs::forms.F22.partials.print_byID_f22_form')->with(compact('header', 'details', 'settings'));
    }

    public function saveF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            $data_header = array(
                'form_no' => $data['F22_from_no'],
                'business_id' => $business_id,
                'location_id' => $data['f22_location_id'],
                'manager_name' => $data['manager_name'],
                'form_date' => date('Y-m-d'),
                'purchase_price1' => !empty($data['purchase_price1']) ? $data['purchase_price1'] : 0.00,
                'purchase_price2' => !empty($data['purchase_price2']) ? $data['purchase_price2'] : 0.00,
                'purchase_price3' => !empty($data['purchase_price3']) ? $data['purchase_price3'] : 0.00,
                'sales_price1' => !empty($data['sales_price1']) ? $data['sales_price1'] : 0.00,
                'sales_price2' => !empty($data['sales_price2']) ? $data['sales_price2'] : 0.00,
                'sales_price3' => !empty($data['sales_price3']) ? $data['sales_price3'] : 0.00,
                'status' => 1,
                'created_by' => Auth::user()->id
            );
            DB::beginTransaction();
            $header = FormF22Header::create($data_header);

            foreach ($data['f22'] as $key => $item) {
                $data_details = array(
                    'header_id' => $header->id,
                    'business_id' => $business_id,
                    'form_no' => $data['F22_from_no'],
                    'location_id' => $data['f22_location_id'],
                    'product_code' => $item['sku'],
                    'product' => $item['product'],
                    'book_no' => $item['book_no'],
                    'current_stock' => $item['current_stock'],
                    'stock_count' => $item['stock_count'],
                    'unit_purchase_price' => $item['unit_purchase_price'],
                    'unit_sale_price' => $item['unit_sale_price'],
                    'purchase_price_total' => $item['total_purhcase_value'],
                    'sales_price_total' => $item['total_sale_value'],
                    'difference_qty' => $item['qty_difference'],
                    'status' => 1
                );

                if (empty($item['stock_count'])) {
                    $difference = 0;
                } else {
                    $difference = $item['stock_count'] - $item['current_stock'];
                }
                $details = FormF22Detail::create($data_details);
                //Adjust Quantity in variations location table
                $settings = MpcsFormSetting::where('business_id', $business_id)->first();

                if (!empty($settings)) {
                    if ($settings->current_stock_aa_onstocktaking == 1) {
                        VariationLocationDetails::where('variation_id', $item['variation_id'])
                            ->where('product_id', $key)
                            ->increment('qty_available', $difference);
                    }
                }
            }
            DB::commit();

            return $this->printF22FormById($header->id);
            $output = [
                'success' => 1,
                'msg' => __('customer.')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }
}
