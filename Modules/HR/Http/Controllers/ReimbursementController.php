<?php

namespace Modules\HR\Http\Controllers;

use App\BusinessLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\Reimbursement;
use Yajra\DataTables\Facades\DataTables;

class ReimbursementController extends Controller
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

            $reimbursements = Reimbursement::leftjoin('employees', 'reimbursements.employee_id', 'employees.id')
                ->leftjoin('departments', 'reimbursements.department_id', 'departments.id')
                ->select(
                    'reimbursements.date',
                    'reimbursements.id',
                    'reimbursements.amount',
                    'reimbursements.memo as desc',
                    'reimbursements.approved_manager',
                    'reimbursements.approved_admin',
                    'employees.first_name',
                    'employees.last_name',
                    'departments.department'
                );


            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $reimbursements->where('location_id', $location_id);
                }
            }

            return DataTables::of($reimbursements)
                ->addColumn(
                    'action',
                    '
                    <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                    </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a class="btn-modal eye_modal" 
                data-href="{{action(\'ReimbursementController@show\', [$id])}}" 
                data-container=".reimbursement_view_modal">
                <i class="fa fa-eye" style="color:#46c37b;"></i> @lang("messages.view") </a></li>

                <li><a class="btn-modal eye_modal" 
                data-href="{{action(\'ReimbursementController@edit\', [$id])}}" 
                data-container=".reimbursement_edit_modal">
                <i class="glyphicon glyphicon-edit" style="color:#46c37b;"></i> @lang("messages.edit") </a></li>

             
                <li><a data-href="{{action(\'ReimbursementController@destroy\', [$id])}}" class="delete_reimbursement"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a></li>
               
                </ul></div>
                    '
                )
                ->removeColumn('id')
                ->editColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })

                ->editColumn('title', function ($row) {
                    if (!empty($row->title)) {
                        $job_title = DB::table('job_title')->where('id', $row->title)->first()->job_title;
                    } else {
                        $job_title = '';
                    }
                    return $job_title;
                })

                ->rawColumns(['name', 'action'])
                ->make(true);
        }


        $business_id = request()->session()->get('user.business_id');
        $department = Department::get();
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('hr::reimbursement.index')->with(compact('business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $all_department = Department::where('business_id', $business_id)->get();

        $employee = Employee::where('business_id', $business_id)->get();


        return view('hr::reimbursement.create')->with(compact('all_department', 'employee'));
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
            $data = array(
                'date' => date('Y-m-d', strtotime($request->date)),
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'amount' => $request->amount,
                'memo' => $request->memo,
            );
            Reimbursement::insert($data);

            $output = [
                'success' => 1,
                'msg' => __('hr::lang.reiembursment_add_success')
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
        $reimbursement = Reimbursement::leftjoin('employees', 'reimbursements.employee_id', 'employees.id')
            ->leftjoin('departments', 'reimbursements.department_id', 'departments.id')
            ->where('reimbursements.id', $id)
            ->select(
                'reimbursements.date',
                'reimbursements.id',
                'reimbursements.amount',
                'reimbursements.memo as desc',
                'reimbursements.approved_manager',
                'reimbursements.approved_admin',
                'reimbursements.manager_comment',
                'reimbursements.admin_comment',
                'employees.first_name',
                'employees.last_name',
                'departments.department'
            )->first();

        return view('hr::reimbursement.show')->with(compact('reimbursement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reimbursement = Reimbursement::leftjoin('employees', 'reimbursements.employee_id', 'employees.id')
            ->leftjoin('departments', 'reimbursements.department_id', 'departments.id')
            ->where('reimbursements.id', $id)
            ->select(
                'reimbursements.date',
                'reimbursements.id',
                'reimbursements.amount',
                'reimbursements.employee_id',
                'reimbursements.department_id',
                'reimbursements.memo',
                'reimbursements.approved_manager',
                'reimbursements.approved_admin',
                'reimbursements.manager_comment',
                'reimbursements.admin_comment',
                'employees.first_name',
                'employees.last_name',
                'departments.department'
            )->first();

        $business_id = request()->session()->get('user.business_id');
        $all_department = Department::where('business_id', $business_id)->get();

        $employee = Employee::where('business_id', $business_id)->get();


        return view('hr::reimbursement.edit')->with(compact('reimbursement', 'all_department', 'employee'));
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
            $data = array(
                'date' => date('Y-m-d', strtotime($request->date)),
                'amount' => $request->amount,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'memo' => $request->memo,
                'approved_manager' => $request->approved_manager,
                'approved_admin' => $request->approved_admin,
                'manager_comment' => $request->manager_comment,
                'admin_comment' => $request->admin_comment,
            );

            Reimbursement::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __('hr::lang.reiembursment_update_success')
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $reimbursement = Reimbursement::where('id', $id)->first();
                $reimbursement->delete();

                $output = [
                    'success' => true,
                    'msg' => __("hr::lang.reimbursement_delete_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
