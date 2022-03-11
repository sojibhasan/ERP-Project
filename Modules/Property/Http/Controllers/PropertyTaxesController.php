<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\PropertyTax;
use Yajra\DataTables\Facades\DataTables;

class PropertyTaxesController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $peroperty_taxes = PropertyTax::leftjoin('business_locations', 'property_taxes.location_id', 'business_locations.id')->where('property_taxes.business_id', $business_id)
                ->select([
                    'property_taxes.*',
                    'business_locations.name as location_name'
                ]);

            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\PropertyTaxesController@edit\', [$id])}}" data-container=".unit_modal" class="btn btn-xs btn-modal btn-primary edit_property_tax_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\PropertyTaxesController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_property_tax_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('tax_type', '{{ucfirst($tax_type)}}')
                ->editColumn('value', '{{@num_format($value)}}')
                ->removeColumn('id')
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::setting.property_taxes.create')
            ->with(compact('quick_add', 'business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'tax_name', 'tax_type']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            if ($input['tax_type'] == 'fixed') {
                $input['value'] = $request->fixed_value;
            }
            if ($input['tax_type'] == 'percentage') {
                $input['value'] = $request->percentage_value;
            }

            $tax = PropertyTax::create($input);
            $output = [
                'success' => true,
                'tab' => 'taxes',
                'msg' => __("property::lang.property_tax_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'taxes',
                'msg' => __("messages.something_went_wrong")
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $property_tax = PropertyTax::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            return view('property::setting.property_taxes.edit')
                ->with(compact('property_tax', 'business_locations'));
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['location_id', 'tax_name', 'tax_type']);
                $business_id = $request->session()->get('user.business_id');

                if ($input['tax_type'] == 'fixed') {
                    $input['value'] = $request->fixed_value;
                }
                if ($input['tax_type'] == 'percentage') {
                    $input['value'] = $request->percentage_value;
                }

                $tax = PropertyTax::where('business_id', $business_id)->findOrFail($id);
                $tax->location_id = $input['location_id'];
                $tax->tax_name = $input['tax_name'];
                $tax->tax_type = $input['tax_type'];
                $tax->value = $input['value'];

                $tax->save();

                $output = [
                    'success' => true,
                    'tab' => 'taxes',
                    'msg' => __("property::lang.property_tax_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'taxes',
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return redirect()->back()->with('status', $output);
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                PropertyTax::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.property_tax_deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => '__("messages.something_went_wrong")'
                ];
            }

            return $output;
        }
    }
}
