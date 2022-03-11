<?php

namespace App\Http\Controllers;

use App\Category;
use App\MergedSubCategory;
use Illuminate\Http\Request;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MergedSubCategoryController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

            $merged_sub_category = MergedSubCategory::leftjoin('categories', 'merged_sub_categories.category_id', 'categories.id')
                ->leftjoin('users', 'merged_sub_categories.created_by', 'users.id')
                ->where('merged_sub_categories.business_id', $business_id)
                ->select('merged_sub_categories.*', 'categories.name as category_name', 'users.username');

            return DataTables::of($merged_sub_category)
                ->addColumn(
                    'action',
                    '
                    @can("category.update")
                    <button data-href="{{action(\'MergedSubCategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("category.delete")
                        <a href="{{action(\'MergedSubCategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_merge_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a>
                    @endcan
                  
                    '
                )
                ->editColumn('merged_sub_categories', function ($row) {
                    $cat = Category::whereIn('id', $row->sub_categories)->pluck('name')->toArray();
                    return $cat;
                    return implode(',', $cat);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return 'Inactive';
                    } else {
                        return 'Active';
                    }
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'merged_sub_categories'])
                ->make(true);
        }

        return view('merged_sub_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');

        return view('merged_sub_categories.create')
            ->with(compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $data = array(
                'business_id' => $business_id,
                'date_and_time' => Carbon::parse($request->date_and_time)->format('Y-m-d'),
                'category_id' => $request->category_id,
                'merged_sub_category_name' => $request->merged_sub_category_name,
                'sub_categories' => $request->sub_categories,
                'status' => $request->status,
                'created_by' => Auth::user()->id
            );

            MergedSubCategory::create($data);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.merge_success')
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
        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $merge = MergedSubCategory::find($id);
        $sub_categories = Category::where('business_id', $business_id)
            ->where('parent_id',  $merge->category_id)
            ->pluck('name', 'id');


        return view('merged_sub_categories.edit')
            ->with(compact('categories', 'sub_categories', 'merge'));
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
        try {
            $business_id = request()->session()->get('user.business_id');
            $data = array(
                'business_id' => $business_id,
                'date_and_time' => Carbon::parse($request->date_and_time)->format('Y-m-d'),
                'category_id' => $request->category_id,
                'merged_sub_category_name' => $request->merged_sub_category_name,
                'sub_categories' => json_encode($request->sub_categories),
                'status' => $request->status,
                'created_by' => Auth::user()->id
            );

            MergedSubCategory::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.merge_update_success')
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            MergedSubCategory::where('id', $id)->delete();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.merge_delete_success')
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

    public function getSubCategories($id)
    {
        $sub_categories = Category::where('parent_id', $id)->select('name', 'id')->get();

        $html = '<option value="">Please Select</option>';

        foreach ($sub_categories as $sub_cat) {
            $html .= '<option value="' . $sub_cat->id . '">' . $sub_cat->name . '</option>';
        }

        return $html;
    }
}
