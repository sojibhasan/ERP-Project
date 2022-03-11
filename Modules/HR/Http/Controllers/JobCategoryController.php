<?php

namespace Modules\HR\Http\Controllers;

use App\Business;
use App\Scopes\HrSettingScope;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\HR\Entities\JobCategory;
use Yajra\DataTables\Facades\DataTables;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->is_superadmin_page) {
                $job_categories = JobCategory::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1);
            } else {
                $job_categories = JobCategory::where('business_id', $business_id);
            }

            return DataTables::of($job_categories)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <a class="btn-modal eye_modal  btn-xs btn-primary" 
                        data-href="{{action(\'\Modules\HR\Http\Controllers\JobCategoryController@edit\', [$id])}}" 
                        data-container=".jobcategory_edit_modal">
                        <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit") </a>
                        &nbsp;
                        <a data-href="{{action(\'\Modules\HR\Http\Controllers\JobCategoryController@destroy\', [$id])}}" class="delete_jobcategory  btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->removeColumn('id')


                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.job_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $data = array(
                'business_id' => $business_id,
                'category_name' => $request->input('category_name')
            );

            DB::beginTransaction();
            if(!empty($request->is_superadmin_default)){
                $data['is_superadmin_default'] = 1;  // true for superadmin settings page
                $job_title = JobCategory::create($data);
                $businesses = Business::all();
                // create defaults for all business
                foreach($businesses as $business){
                    $data['is_default'] = $job_title->id; // superadmin settings created default id reference
                    $data['business_id'] = $business->id;
                    $data['is_superadmin_default'] = 0;
                    JobCategory::create($data);
                }
            }else{
                JobCategory::create($data);
            }
            DB::commit();

            $output = [
                'success' => 1,
                'tab' => 'job_category',
                'msg' => __('hr.job_category_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'job_category',
                'msg' => __('messages.something_went_wrong')
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
        $jobcategory = JobCategory::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->first();
        return view('hr::settings.job_category.edit')->with(compact('jobcategory'));
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
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }


        try {
            $edit_job_category = JobCategory::withoutGlobalScope(HrSettingScope::class)->findOrFail($id);
            $data = array(
                'category_name' => $request->input('category_name')
            );

            JobCategory::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->update($data);

            if($edit_job_category->is_superadmin_default){
                JobCategory::withoutGlobalScope(HrSettingScope::class)->where('is_default', $id)->update($data);
            }

            $output = [
                'success' => 1,
                'tab' => 'job_category',
                'msg' => __('hr.job_category_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'job_category',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
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
            $delete_job_title = JobCategory::withoutGlobalScope(HrSettingScope::class)->findOrFail($id);
            if($delete_job_title->is_superadmin_default){
                JobCategory::withoutGlobalScope(HrSettingScope::class)->where('is_default', $id)->delete();
            }
            JobCategory::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr.job_category_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
}
