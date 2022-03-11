<?php

namespace Modules\Ran\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldSmith;
use Modules\Ran\Entities\Wastage;
use Yajra\DataTables\Facades\DataTables;

class WastageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $wastages = Wastage::where('wastages.business_id', $business_id)
                ->leftjoin('categories', 'wastages.sub_category_id', 'categories.id')
                ->leftjoin('users', 'wastages.created_by', 'users.id')
                ->leftjoin('business_locations', 'wastages.location_id', 'business_locations.id')
                ->leftjoin('gold_smiths', 'wastages.goldsmith_id', 'gold_smiths.id')
                ->select([
                    'wastages.*',
                    'business_locations.name as location',
                    'users.username as user',
                    'categories.name as sub_category',
                    'gold_smiths.name as goldsmith'
                ]);
            return DataTables::of($wastages)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\WastageController@edit', [$row->id]) . '" data-container=".goldsmith_model" class="btn-modal wastage_edit"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\WastageController@destroy', [$row->id]) . '" class="delete-wastage"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $wastage_form_no = Wastage::where('business_id', $business_id)->count() + 1;
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths = GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');

        return view('ran::goldsmith.wastage.create')->with(compact(
            'wastage_form_no',
            'business_locations',
            'goldsmiths',
            'categories',
        ));
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
            $input['created_by'] = Auth::user()->id;
            $input['date_and_time'] = !empty($request->date_and_time) ? Carbon::parse($request->date_and_time)->format('Y-m-d H:i:s') : '';

            Wastage::create($input);

            $output = [
                'success' => true,
                'tab' => 'wastage',
                'msg' => __('ran::lang.wastage_create_success')
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
        $business_id = request()->session()->get('business.id');
        $wastage = Wastage::findOrFail($id);
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths = GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');
        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', $wastage->category_id)->pluck('name', 'id');

        return view('ran::goldsmith.wastage.edit')->with(compact(
            'wastage',
            'business_locations',
            'goldsmiths',
            'categories',
            'sub_categories'
        ));
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
            $input['date_and_time'] = !empty($request->date_and_time) ? Carbon::parse($request->date_and_time)->format('Y-m-d H:i:s') : '';

            Wastage::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'wastage',
                'msg' => __('ran::lang.wastage_update_success')
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

            Wastage::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('ran::lang.wastage_delete_success')
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

    /**
     * Display a details of the resource.
     * @return Renderable
     */
    public function getDetails()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $wastages = Wastage::where('wastages.business_id', $business_id)
                ->leftjoin('categories as cat', 'wastages.sub_category_id', 'cat.id')
                ->leftjoin('categories as sub_cat', 'wastages.sub_category_id', 'sub_cat.id')
                ->leftjoin('users', 'wastages.created_by', 'users.id')
                ->leftjoin('business_locations', 'wastages.location_id', 'business_locations.id')
                ->leftjoin('gold_smiths', 'wastages.goldsmith_id', 'gold_smiths.id')
                ->select([
                    'wastages.*',
                    'business_locations.name as location',
                    'users.username as user',
                    'cat.name as category',
                    'sub_cat.name as sub_category',
                    'gold_smiths.name as goldsmith'
                ]);
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $wastages->whereDate('wastages.date_and_time', '>=', request()->start_date)
                    ->whereDate('wastages.date_and_time', '<=', request()->end_date);
            }

            if (!empty(request()->input('category_id'))) {
                $wastages->where('wastages.category_id', request()->input('category_id'));
            }
            if (!empty(request()->input('sub_category_id'))) {
                $wastages->where('wastages.sub_category_id', request()->input('sub_category_id'));
            }
            if (!empty(request()->input('goldsmith_id'))) {
                $wastages->where('wastages.goldsmith_id', request()->input('goldsmith_id'));
            }

            return DataTables::of($wastages)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\WastageController@edit', [$row->id]) . '" data-container=".goldsmith_model" class="btn-modal wastage_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\WastageController@destroy', [$row->id]) . '" class="delete-wastage"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
