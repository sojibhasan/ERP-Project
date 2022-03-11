<?php

namespace Modules\Petro\Http\Controllers;

use App\Product;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Yajra\DataTables\Facades\DataTables;

class PumpOperatorAssignmentController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $businessUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('petro::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('petro::create');
    }

    public function getPumperAssignment($pump_id, $pump_operator_id)
    {
        $business_id = Auth::user()->business_id;

        $pump = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->where('pumps.id', $pump_id)
            ->where('pumps.business_id', $business_id)
            ->select('pumps.*', 'products.name')->first();

        if(empty(session()->get('pump_operator_main_system'))){
            $layout = 'pumper';
        }else{
            $layout = 'app';
        }

        return view('petro::pump_operators.partials.pumper_assignment')->with(compact(
            'pump',
            'pump_operator_id',
            'layout'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = Auth::user()->business_id;
        try {
            $input = $request->except('_token');
            $input['date_and_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $input['status'] = !empty($input['status']) ? 'open' : 'close';
            $input['business_id'] = $business_id;

            if (!empty($input['closing_meter'])) {
                if ($input['closing_meter'] < $input['starting_meter']) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_smaller')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }
            if (!empty($input['status'])) {
                if (empty($input['closing_meter'])) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_empty')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }

            PumpOperatorAssignment::create($input);

            $output = [
                'success' => true,
                'msg' => __('petro::lang.pump_operator_assigned_success')
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('petro::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $pump_assignment = PumpOperatorAssignment::findOrFail($id);

        if(empty(session()->get('pump_operator_main_system'))){
            $layout = 'pumper';
        }else{
            $layout = 'app';
        }

        return view('petro::pump_operators.partials.pumper_assignment_edit')->with(compact('pump_assignment', 'layout'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
          try {
            $input = $request->except('_token', '_method');
            $input['status'] = !empty($input['status']) ? 'open' : 'close';

            if (!empty($input['closing_meter'])) {
                if ($input['closing_meter'] < $input['starting_meter']) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_smaller')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }
            if (!empty($input['status'])) {
                if (empty($input['closing_meter'])) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_empty')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }

            PumpOperatorAssignment::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            PumpOperatorAssignment::findOrFail($id)->delete();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

}
