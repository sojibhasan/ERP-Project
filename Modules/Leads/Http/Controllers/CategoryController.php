<?php

namespace Modules\Leads\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Leads\Entities\LeadsCategory;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $leads_categories = LeadsCategory::leftjoin('users', 'leads_categories.created_by', 'users.id')
                ->where('leads_categories.business_id', $business_id)
                ->select([
                    'leads_categories.*',
                    'users.username as user'
                ]);

            if (!empty(request()->category)) {
                $leads_categories->where('leads_categories.id', request()->category);
            }
            if (!empty(request()->user)) {
                $leads_categories->where('leads_categories.created_by', request()->user);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $leads_categories->whereDate('date', '>=', request()->start_date);
                $leads_categories->whereDate('date', '<=', request()->end_date);
            }

            return DataTables::of($leads_categories)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\CategoryController@edit\',[$id])}}" data-container=".category_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\CategoryController@destroy\',[$id])}}" class="btn btn-xs btn-danger leads_category_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('leads::settings.category.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['name'] = $request->name;
            $input['created_by'] = Auth::user()->id;
            $input['business_id'] = $business_id;

            LeadsCategory::create($input);

            $output = [
                'success' => true,
                'tab' => 'category',
                'msg' => __('leads::lang.category_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'category',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $category = LeadsCategory::findOrFail($id);
        return view('leads::settings.category.edit')->with(compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['name'] = $request->name;

            LeadsCategory::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'category',
                'msg' => __('leads::lang.category_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'category',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            LeadsCategory::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('leads::lang.category_delete_success')
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
