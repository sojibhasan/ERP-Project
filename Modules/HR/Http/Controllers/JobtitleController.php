<?php

namespace Modules\HR\Http\Controllers;

use App\Business;
use App\Scopes\HrSettingScope;
use Modules\HR\Entities\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JobtitleController extends Controller
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
                $job_titles = JobTitle::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1);
            } else {
                $job_titles = JobTitle::where('business_id', $business_id);
            }

            return DataTables::of($job_titles)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                            <a class="btn-modal eye_modal btn-xs btn-primary" 
                            data-href="{{action(\'\Modules\HR\Http\Controllers\JobtitleController@edit\', [$id])}}" 
                            data-container=".jobtitle_edit_modal">
                            <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit") </a>
                            &nbsp;
                            <a data-href="{{action(\'\Modules\HR\Http\Controllers\JobtitleController@destroy\', [$id])}}" class="delete_jobtitle btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a>
                        </div>'
                )
                ->removeColumn('id')


                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.job_title.index');
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
            'job_title' => 'required|string'
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
                'job_title' => $request->input('job_title'),
                'description' => $request->input('description'),
            );

            DB::beginTransaction();
            if(!empty($request->is_superadmin_default)){
                $data['is_superadmin_default'] = 1;  // true for superadmin settings page
                $job_title = JobTitle::create($data);
                $businesses = Business::all();
                // create defaults for all business
                foreach($businesses as $business){
                    $data['is_default'] = $job_title->id; // superadmin settings created default id reference
                    $data['business_id'] = $business->id;
                    $data['is_superadmin_default'] = 0;
                    JobTitle::create($data);
                }
            }else{
                JobTitle::create($data);
            }
            DB::commit();

            $output = [
                'success' => 1,
                'tab' => 'job_title',
                'msg' => __('hr::lang.job_title_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'job_title',
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
        $jobtitle = JobTitle::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->first();
        return view('hr::settings.job_title.edit')->with(compact('jobtitle'));
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
            'job_title' => 'required|string'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }


        try {
            $edit_total_title = JobTitle::withoutGlobalScope(HrSettingScope::class)->findOrFail($id);
            $data = array(
                'job_title' => $request->input('job_title'),
                'description' => $request->input('description'),
            );

            JobTitle::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->update($data);

            if($edit_total_title->is_superadmin_default){
                JobTitle::withoutGlobalScope(HrSettingScope::class)->where('is_default', $id)->update($data);
            }

            $output = [
                'success' => 1,
                'tab' => 'job_title',
                'msg' => __('hr::lang.job_title_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'job_title',
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
            $delete_job_title = JobTitle::withoutGlobalScope(HrSettingScope::class)->findOrFail($id);
            if($delete_job_title->is_superadmin_default){
                JobTitle::where('is_default', $id)->delete();
            }
            JobTitle::withoutGlobalScope(HrSettingScope::class)->where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang..job_title_delete_success')
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
