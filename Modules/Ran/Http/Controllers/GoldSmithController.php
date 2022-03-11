<?php

namespace Modules\Ran\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldSmith;
use Yajra\DataTables\Facades\DataTables;

class GoldSmithController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $gold_smiths = GoldSmith::where('gold_smiths.business_id', $business_id)
                ->select([
                    'gold_smiths.*'
                ]);
            return DataTables::of($gold_smiths)
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
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\GoldSmithController@edit', [$row->id]) . '" data-container=".goldsmith_model" class="btn-modal goldsmith_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\GoldSmithController@destroy', [$row->id]) . '" class="delete-goldsmith"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths = GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');

        return view('ran::goldsmith.index')->with(compact(
            'business_locations',
            'goldsmiths',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('ran::goldsmith.goldsmith.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['dob'] = !empty($request->dob) ? Carbon::parse($request->dob)->format('Y-m-d') : '';

            GoldSmith::create($input);

            $output = [
                'success' => true,
                'msg' => __('ran::lang.goldsmith_create_success')
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
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $goldsmith = GoldSmith::findOrFail($id);

        return view('ran::goldsmith.goldsmith.edit')->with(compact('goldsmith'));
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
            $business_id = $request->session()->get('business.id');
            $input = $request->except('_token', '_method');
            $input['dob'] = !empty($request->dob) ? Carbon::parse($request->dob)->format('Y-m-d') : '';

            GoldSmith::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __('ran::lang.goldsmith_update_success')
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

            GoldSmith::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('ran::lang.goldsmith_delete_success')
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
