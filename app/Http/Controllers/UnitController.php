<?php

namespace App\Http\Controllers;

use App\Unit;
use App\Product;
use App\Utils\ModuleUtil;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\Util;
use Modules\Superadmin\Entities\HelpExplanation;

class UnitController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('unit.view') && !auth()->user()->can('unit.create') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_property = request()->is_property;
         
            $unit = Unit::where('business_id', $business_id)
                        ->with(['base_unit'])
                        ->select(['actual_name', 'short_name', 'allow_decimal', 'id',
                            'base_unit_id', 'base_unit_multiplier']);
            if(!empty($is_property)){
                $unit->where('is_property', 1);
            }else{
                $unit->where('is_property', 0);
            }
            return Datatables::of($unit)
                ->addColumn(
                    'action',
                    '@if(auth()->user()->can("unit.update") ||  auth()->user()->can("property.settings.unit"))
                    <button data-href="{{action(\'UnitController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_unit_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endif
                    @if(auth()->user()->can("unit.delete") ||  auth()->user()->can("property.settings.unit"))
                        <button data-href="{{action(\'UnitController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_unit_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endif'
                )
                ->editColumn('allow_decimal', function ($row) {
                    if ($row->allow_decimal) {
                        return __('messages.yes');
                    } else {
                        return __('messages.no');
                    }
                })
                ->editColumn('multiple_units', function ($row) {
                    $html = __('unit.base_unit');
                    if ($row->base_unit_id) {
                        $html = __('unit.multiple_unit');
                       
                    } 
                    return $html;
                })
                ->editColumn('connected_units', function ($row) {
                    $html = '';
                    if (!$row->base_unit_id) {
                        $base_unit = Unit::where('base_unit_id', $row->id)->pluck('actual_name')->toArray();
                        $html = $base_unit;
                    } 
                    return $html;
                })
                ->editColumn('actual_name', function ($row) {
                    if (!empty($row->base_unit_id)) {
                        return  $row->actual_name . ' (' . (float)$row->base_unit_multiplier . $row->base_unit->short_name . ')';
                    }
                    return  $row->actual_name;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'multiple_units'])
                ->make(true);
        }

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        return view('unit.index')->with(compact('help_explanations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $is_property = request()->is_property;
        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        if($is_property){
            $units = Unit::getPropertyUnitDropdown($business_id);
        }else{
            $units = Unit::forDropdown($business_id);

        }
        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $sale_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sale_module');
        $property_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');

        return view('unit.create')
                ->with(compact('quick_add', 'units', 'help_explanations', 'is_property', 'sale_module', 'property_module'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['is_property'] = !empty($request->is_property) ? 1 : 0;
            $input['created_by'] = $request->session()->get('user.id');
            $input['show_in_add_product_unit'] = !empty($request->show_in_add_product_unit) ? 1 : 0;
            $input['show_in_add_pos_unit'] = !empty($request->show_in_add_pos_unit) ? 1 : 0;
            $input['show_in_add_sale_unit'] = !empty($request->show_in_add_sale_unit) ? 1 : 0;
            $input['show_in_add_project_unit'] = !empty($request->show_in_add_project_unit) ? 1 : 0;
            $input['show_in_sell_land_block_unit'] = !empty($request->show_in_sell_land_block_unit) ? 1 : 0;

            if ($request->has('define_base_unit')) {
                if (!empty($request->input('base_unit_id')) && !empty($request->input('base_unit_multiplier'))) {
                    $base_unit_multiplier = $this->commonUtil->num_uf($request->input('base_unit_multiplier'));
                    if ($base_unit_multiplier != 0) {
                        $input['base_unit_id'] = $request->input('base_unit_id');
                        $input['base_unit_multiplier'] = $base_unit_multiplier;
                    }
                }
            }

            $unit = Unit::create($input);
            $output = ['success' => true,
                        'data' => $unit,
                        'msg' => __("unit.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
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
        if (!auth()->user()->can('unit.update') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $unit = Unit::where('business_id', $business_id)->find($id);

            if($unit->is_property){
                $units = Unit::getPropertyUnitDropdown($business_id);
            }else{
                $units = Unit::forDropdown($business_id);
            }

            $sale_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sale_module');
            $property_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');
            return view('unit.edit')
                ->with(compact('unit', 'units', 'sale_module', 'property_module'));
        }
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
        if (!auth()->user()->can('unit.update') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
                $business_id = $request->session()->get('user.business_id');

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);
                $unit->actual_name = $input['actual_name'];
                $unit->short_name = $input['short_name'];
                $unit->allow_decimal = $input['allow_decimal'];
                $unit->show_in_add_product_unit = !empty($request->show_in_add_product_unit) ? 1 : 0;
                $unit->show_in_add_pos_unit = !empty($request->show_in_add_pos_unit) ? 1 : 0;
                $unit->show_in_add_sale_unit = !empty($request->show_in_add_sale_unit) ? 1 : 0;
                $unit->show_in_add_project_unit = !empty($request->show_in_add_project_unit) ? 1 : 0;
                $unit->show_in_sell_land_block_unit = !empty($request->show_in_sell_land_block_unit) ? 1 : 0;

                if ($request->has('define_base_unit')) {
                    if (!empty($request->input('base_unit_id')) && !empty($request->input('base_unit_multiplier'))) {
                        $base_unit_multiplier = $this->commonUtil->num_uf($request->input('base_unit_multiplier'));
                        if ($base_unit_multiplier != 0) {
                            $unit->base_unit_id = $request->input('base_unit_id');
                            $unit->base_unit_multiplier = $base_unit_multiplier;
                        }
                    }
                } else {
                    $unit->base_unit_id = null;
                    $unit->base_unit_multiplier = null;
                }

                $unit->save();

                $output = ['success' => true,
                            'msg' => __("unit.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('unit.delete') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);

                //check if any product associated with the unit
                $exists = Product::where('unit_id', $unit->id)
                                ->exists();
                if (!$exists) {
                    $unit->delete();
                    $output = ['success' => true,
                            'msg' => __("unit.deleted_success")
                            ];
                } else {
                    $output = ['success' => false,
                            'msg' => __("lang_v1.unit_cannot_be_deleted")
                            ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => '__("messages.something_went_wrong")'
                        ];
            }

            return $output;
        }
    }

    public function getSubUnits(Request $request){
        $unit_id = $request->unit_id;

        $units = Unit::where('id',  $unit_id)->orWhere('base_unit_id',  $unit_id)->get();

        return ['sub_units' => $units, 'count' => $units->count()];
    }
}
