<?php

namespace App\Http\Controllers;

use App\CrmActivity;
use App\CrmActivityDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\Reminder;

class CrmActivityDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $input['crm_activity_id'] = $request->crm_activity_id;
            $input['date'] = Carbon::parse($request->date)->format('Y-m-d');
            $input['note'] = $request->note;
            $input['next_follow_up_date'] =  Carbon::parse($request->next_follow_up_date)->format('Y-m-d H:i:s');

            CrmActivityDetail::create($input);

            CrmActivity::where('id', $request->crm_activity_id)->update(['discontinue_follow_up' => $request->discontinue_follow_up]);
            Reminder::where('crm_reminder_id',  $request->crm_activity_id)->update(['snoozed_at' => Carbon::parse($request->next_follow_up_date)->format('Y-m-d H:i:s'), 'snooze' => 1]);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
