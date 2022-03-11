<?php

namespace Modules\MPCS\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\FormF17Detail;
use Modules\MPCS\Entities\FormF17Header;
use Modules\MPCS\Entities\FormF17HeaderController;
use Modules\MPCS\Entities\FormF22Header;

class F17FormController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $count = FormF17Header::where('business_id', $business_id)->count();
        if (!empty($settings)) {
            $F17_from_no = $settings->F17_form_sn + $count;
        } else {
            $F17_from_no = 1 + $count;
        }

        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        $categories = Category::forDropdown($business_id);

        $brands = Brands::forDropdown($business_id);

        $units = Unit::forDropdown($business_id);

        $business_locations = BusinessLocation::forDropdown($business_id);

        $forms_nos = FormF17Header::where('business_id', $business_id)->pluck('form_no', 'id');

        return view('mpcs::forms.F17.index')->with(compact(
            'stores',
            'products',
            'categories',
            'brands',
            'units',
            'business_locations',
            'F17_from_no',
            'forms_nos'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details as vld', 'variations.id', 'vld.variation_id')
                ->where('products.business_id', $business_id)
                ->select(
                    'products.id as p_id',
                    'products.name as product',
                    'products.sku as sku',
                    'variations.default_sell_price as unit_price',
                    'vld.qty_available as current_stock',
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $products->whereIn('vld.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $products->where('vld.location_id', request()->location_id);
            }
            if (!empty(request()->category_id)) {
                $products->where('products.category_id', request()->category_id);
            }
            if (!empty(request()->unit_id)) {
                $products->where('products.unit_id', request()->unit_id);
            }
            if (!empty(request()->brand_id)) {
                $products->where('products.brand_id', request()->brand_id);
            }


            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);

            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('product', function ($row) use ($business_details) {
                    return '<span>' . $row->product . '</span><input type="hidden" value="' . $row->product . '" name="F17[' . $row->p_id . '][product]" id="F17[' . $row->p_id . '][product]">';
                })
                ->editColumn('sku', function ($row) use ($business_details) {
                    return '<span>' . $row->sku . '</span><input type="hidden" value="' . $row->sku . '" name="F17[' . $row->p_id . '][sku]" id="F17[' . $row->p_id . '][sku]">';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price" data-orig-value="' . $row->unit_price . '">' . $row->unit_price . '</span><input type="hidden" value="' . $row->unit_price . '" name="F17[' . $row->p_id . '][unit_price]" id="F17[' . $row->p_id . '][unit_price]">';
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    return '<span  class="current_stock" data-orig-value="' . $row->current_stock . '">' . $row->current_stock . '</span><input type="hidden" value="' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '" name="F17[' . $row->p_id . '][current_stock]" id="F17[' . $row->p_id . '][current_stock]">';
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    $html = '<select name="F17[' . $row->p_id . '][select_mode]" id="F17[' . $row->p_id . '][select_mode]" class="form-control select_mode input_number" placeholder="Please Select">
                        <option value="increase">Increase</option>
                        <option value="decrease">Decrease</option>
                    </select>';
                    return $html;
                })
                ->addColumn('unit_price_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price_difference" data-orig-value="' . $row->unit_price_difference . '" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][unit_price_difference]" id="F17[' . $row->p_id . '][unit_price_difference]" class="unit_price_difference_value">';
                })
                ->addColumn('new_price', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->p_id . '][new_price]" id="F17[' . $row->p_id . '][new_price]" class="form-control input_number new_price_value">';
                })
                ->addColumn('price_changed_loss', function ($row) {
                    return '<span class="display_currency price_changed_loss" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][price_changed_loss]" id="F17[' . $row->p_id . '][price_changed_loss]" class="price_changed_loss_value">';
                })
                ->addColumn('price_changed_gain', function ($row) {
                    return '<span class="display_currency price_changed_gain" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][price_changed_gain]" id="F17[' . $row->p_id . '][price_changed_gain]" class="price_changed_gain_value">';
                })
                ->addColumn('page_no', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->p_id . '][page_no]" id="F17[' . $row->p_id . '][page_no]" class="form-control input_number page_no">';
                })
                ->addColumn('signature', function ($row) {
                    return '';
                })
                ->removeColumn('id')

                ->rawColumns(['sku', 'unit_price', 'current_stock', 'product', 'select_mode', 'unit_price_difference', 'new_price', 'price_changed_loss', 'price_changed_gain', 'page_no'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            $data_array = $data['F17'];

            $header_data = array(
                'business_id' => $business_id,
                'date' => !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : null,
                'form_no' => $request->form_no,
                'location_id' => $request->location_id,
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'brand_id' => $request->brand_id,
                'total_price_change_loss' => $request->total_price_change_loss,
                'total_price_change_gain' => $request->total_price_change_gain,
                'page_no' => $request->page_no,
                'user' => Auth::user()->id
            );
            DB::beginTransaction();
            $header = FormF17Header::create($header_data);

            $total_price_change_loss = 0;
            $total_price_change_gain = 0;
            foreach ($data_array as $key => $item) {
                $array_details = array(
                    'header_id' => $header->id,
                    'product_id' => $key,
                    'sku' => $item['sku'],
                    'product' => $item['product'],
                    'current_stock' => $item['current_stock'],
                    'unit_price' => $item['unit_price'],
                    'select_mode' => $item['select_mode'],
                    'new_price' => $item['new_price'],
                    'unit_price_difference' => $item['unit_price_difference'],
                    'price_changed_loss' => $item['price_changed_loss'],
                    'price_changed_gain' => $item['price_changed_gain'],
                    'page_no' => $item['page_no'],
                );

                FormF17Detail::create($array_details);
                $total_price_change_loss += !empty($item['price_changed_loss']) ? $item['price_changed_loss'] : 0;
                $total_price_change_gain += !empty($item['price_changed_gain']) ? $item['price_changed_gain'] : 0;
            }
            $header->total_price_change_loss = $total_price_change_loss;
            $header->total_price_change_gain = $total_price_change_gain;
            $header->save();
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('mpcs::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * list the specified resource.
     * @return Response
     */
    public function list()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $products = FormF17Header::leftjoin('categories', 'form_f17_headers.category_id', 'categories.id')
                ->leftjoin('categories as sub_cat', 'form_f17_headers.sub_category_id', 'sub_cat.id')
                ->leftjoin('business_locations', 'form_f17_headers.location_id', 'business_locations.id')
                ->leftjoin('units', 'form_f17_headers.unit_id', 'units.id')
                ->leftjoin('brands', 'form_f17_headers.brand_id', 'brands.id')
                ->leftjoin('users', 'form_f17_headers.user', 'users.id')
                ->where('form_f17_headers.business_id', $business_id)
                ->select(
                    'form_f17_headers.*',
                    'categories.name as category',
                    'sub_cat.name as sub_category',
                    'business_locations.name as location',
                    'units.actual_name as unit',
                    'brands.name as brands',
                    'users.username'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $products->whereIn('form_f17_headers.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $products->where('form_f17_headers.location_id', request()->location_id);
            }
            if (!empty(request()->category_id)) {
                $products->where('form_f17_headers.category_id', request()->category_id);
            }
            if (!empty(request()->unit_id)) {
                $products->where('form_f17_headers.unit_id', request()->unit_id);
            }
            if (!empty(request()->brand_id)) {
                $products->where('form_f17_headers.brand_id', request()->brand_id);
            }

            $start_date = Carbon::parse(request()->start_date)->format('Y-m-d');
            $end_date = Carbon::parse(request()->end_date)->format('Y-m-d');

            if (!empty($start_date) && !empty($end_date)) {
                $products->whereBetween('form_f17_headers.date', [request()->start_date, request()->end_date]);
            }


            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);

            return DataTables::of($products)
                ->addColumn('action', function ($row) use ($business_details) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can("edit_f17_form")) {
                        $html .= '<li><a target="_blank" href="' . action('\Modules\MPCS\Http\Controllers\F17FormController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    return '';
                })
                ->addColumn('store', function ($row) use ($business_details) {
                    return '';
                })
                ->removeColumn('id')

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('mpcs::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
     
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $form = FormF17Header::leftjoin('form_f17_details', 'form_f17_headers.id', 'form_f17_details.header_id')
            ->where('form_f17_headers.id', $id)
            ->select('form_f17_headers.*', 'form_f17_details.*', 'form_f17_details.id as detial_id');

            $business_details = Business::find($business_id);

            return DataTables::of($form)
                ->addIndexColumn()
                ->editColumn('product', function ($row) use ($business_details) {
                    return '<span>' . $row->product . '</span><input type="hidden" value="' . $row->product . '" name="F17[' . $row->detial_id . '][product]" id="F17[' . $row->detial_id . '][product]">';
                })
                ->editColumn('sku', function ($row) use ($business_details) {
                    return '<span>' . $row->sku . '</span><input type="hidden" value="' . $row->sku . '" name="F17[' . $row->detial_id . '][sku]" id="F17[' . $row->detial_id . '][sku]">';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price" data-orig-value="' . $row->unit_price . '">' . $row->unit_price . '</span><input type="hidden" value="' . $row->unit_price . '" name="F17[' . $row->detial_id . '][unit_price]" id="F17[' . $row->detial_id . '][unit_price]">';
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    return '<span  class="current_stock" data-orig-value="' . $row->current_stock . '">' . $row->current_stock . '</span><input type="hidden" value="' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '" name="F17[' . $row->detial_id . '][current_stock]" id="F17[' . $row->detial_id . '][current_stock]">';
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    $increase = ''; $decrease = '';
                    if($row->select_mode == 'increase'){
                        $increase = 'selected';
                    }else{
                        $decrease = 'selected';
                    }
                    $html = '<select name="F17[' . $row->detial_id . '][select_mode]" id="F17[' . $row->detial_id . '][select_mode]" class="form-control select_mode" placeholder="Please Select">
                        <option '.$increase.' value="increase">Increase</option>
                        <option '.$decrease.' value="decrease">Decrease</option>
                    </select>';
                    return $html;
                })
                ->addColumn('unit_price_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price_difference" data-orig-value="' . $row->unit_price_difference . '" data-currency_symbol = "false">' . $this->productUtil->num_f($row->unit_price_difference, false, $business_details, false) . '</span><input type="hidden" name="F17[' . $row->detial_id . '][unit_price_difference]" id="F17[' . $row->detial_id . '][unit_price_difference]" class="unit_price_difference_value">';
                })
                ->addColumn('new_price', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->detial_id . '][new_price]" id="F17[' . $row->detial_id . '][new_price]" value="'.$row->new_price.'" class="form-control input_number new_price_value">';
                })
                ->addColumn('price_changed_loss', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_loss" data-orig-value="'.$row->price_changed_loss.'" data-currency_symbol = "false">'. $this->productUtil->num_f($row->price_changed_loss, false, $business_details, false).'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_loss]" id="F17[' . $row->detial_id . '][price_changed_loss]" value="'.$row->price_changed_loss.'" class="price_changed_loss_value">';
                })
                ->addColumn('price_changed_gain', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_gain" data-orig-value="'.$row->price_changed_gain.'" data-currency_symbol = "false">'.  $this->productUtil->num_f($row->price_changed_gain, false, $business_details, false) .'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_gain]" id="F17[' . $row->detial_id . '][price_changed_gain]" value="'.$row->price_changed_gain.'" class="price_changed_gain_value">';
                })
                ->addColumn('page_no', function ($row) {
                    return '<input value="'.$row->page_no.'" type="text" style="width: 60px;" name="F17[' . $row->detial_id . '][page_no]" id="F17[' . $row->detial_id . '][page_no]" class="form-control input_number page_no">';
                })
                ->addColumn('signature', function ($row) {
                    return '';
                })
                ->removeColumn('id')

                ->rawColumns(['sku', 'unit_price', 'current_stock', 'product', 'select_mode', 'unit_price_difference', 'new_price', 'price_changed_loss', 'price_changed_gain', 'page_no'])
                ->make(true);
        }

        return view('mpcs::forms.F17.edit')->with(compact('id'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $business_id = session()->get('user.business_id');

        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            $data_array = $data['F17'];

            $header = FormF17Header::findOrFail($id);

            DB::beginTransaction();
            $total_price_change_loss = 0;
            $total_price_change_gain = 0;
            foreach ($data_array as $key => $item) {
                $array_details = array(
                    'sku' => $item['sku'],
                    'product' => $item['product'],
                    'current_stock' => $item['current_stock'],
                    'unit_price' => $item['unit_price'],
                    'select_mode' => $item['select_mode'],
                    'new_price' => $item['new_price'],
                    'unit_price_difference' => $item['unit_price_difference'],
                    'price_changed_loss' => $item['price_changed_loss'],
                    'price_changed_gain' => $item['price_changed_gain'],
                    'page_no' => $item['page_no'],
                );

                FormF17Detail::where('id', $key)->update($array_details);
                $total_price_change_loss += !empty($item['price_changed_loss']) ? $item['price_changed_loss'] : 0;
                $total_price_change_gain += !empty($item['price_changed_gain']) ? $item['price_changed_gain'] : 0;
            }
            $header->total_price_change_loss = $total_price_change_loss;
            $header->total_price_change_gain = $total_price_change_gain;
            $header->save();
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('mpcs::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
