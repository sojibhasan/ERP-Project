<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CrmActivity;
use App\CrmActivityDetail;
use App\CrmGroup;
use App\Customer;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\TasksManagement\Entities\Reminder;
use Yajra\DataTables\Facades\DataTables;

class CRMActivityController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
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

            $query = CrmActivity::where('crm_activities.business_id', $business_id)
                ->leftjoin('crm_activity_details', 'crm_activities.id', 'crm_activity_details.crm_activity_id')
                ->select([
                    'crm_activities.*',
                    'crm_activity_details.note', 'crm_activity_details.next_follow_up_date'

                ])->groupBy('crm_activities.id');

            $crm_activities = DataTables::of($query)
                ->addColumn(
                    'action',
                    '
                <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a data-href="{{action(\'CRMActivityController@edit\', [$id])}}" class="btn-modal edit_crm_activity_button" data-container=".crm_edit"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                <li><a href="{{action(\'CRMActivityController@destroy\', [$id])}}" class="delete_crm_activity_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                 </ul></div>'
                )
                ->editColumn('discontinue_follow_up', function ($row) {
                    if ($row->discontinue_follow_up) {
                        return 'Yes';
                    }

                    return 'No';
                })
                ->removeColumn('id');


            return $crm_activities->rawColumns(['discontinue_follow_up', 'action'])
                ->make(true);
        }

        return view('crm_activity.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }


        $crm_groups = CrmGroup::forDropdown($business_id);

        return view('crm_activity.create')
            ->with(compact('crm_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $input = $request->except('_token', 'contact_id', 'password', 'confirm_password', 'note', 'next_follow_up_date');
            $input['business_id'] = $business_id;
            $input['date'] = Carbon::parse($input['date'])->format('Y-m-d');
            $input['discontinue_follow_up'] = 0;
            DB::beginTransaction();

            $crm_activity = CrmActivity::create($input);

            $details['date'] =     Carbon::parse($input['date'])->format('Y-m-d');
            $details['next_follow_up_date'] = Carbon::parse($request->next_follow_up_date)->format('Y-m-d H:i:s');
            $details['note'] = $request->note;
            $details['crm_activity_id'] = $crm_activity->id;

            $crm_activity_detail = CrmActivityDetail::create($details);


            if ($request->add_in_customer_page) {
                $contact = $this->createContact($request);
                $crm_activity->contact_id = $contact->id;
                $crm_activity->save();
            }

            $reminder_data['business_id'] = $business_id;
            $reminder_data['name'] = $request->name;
            $reminder_data['snoozed_at'] =  Carbon::parse($request->next_follow_up_date)->format('Y-m-d H:i:s');
            $reminder_data['snooze'] =  1;
            $reminder_data['crm_reminder_id'] = $crm_activity->id;
            $reminder_data['options'] = 'when_login';

            Reminder::create($reminder_data);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.crm_activity_create_success')
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
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }


        $crm_groups = CrmGroup::forDropdown($business_id);
        $crm_activity = CrmActivity::findOrFail($id);
        $crm_activity_details = CrmActivityDetail::where('crm_activity_id', $id)->orderBy('id', 'desc')->first();

        return view('crm_activity.edit')
            ->with(compact('crm_groups', 'crm_activity', 'crm_activity_details'));
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
        $business_id = request()->session()->get('user.business_id');
        try {
            $input = $request->except('_token', '_method', 'contact_id', 'password', 'confirm_password', 'note', 'next_follow_up_date');
            $input['business_id'] = $business_id;
            $input['date'] = Carbon::parse($input['date'])->format('Y-m-d');
            $input['discontinue_follow_up'] = 0;
            DB::beginTransaction();

            CrmActivity::where('id', $id)->update($input);

            $details['date'] =     Carbon::parse($input['date'])->format('Y-m-d');
            $details['next_follow_up_date'] = Carbon::parse($request->next_follow_up_date)->format('Y-m-d H:i:s');
            $details['note'] = $request->note;

            $crm_activity_detail = CrmActivityDetail::where('crm_activity_id', $id)->update($details);


            $contact_exist = Contact::where('contact_id', $request->contact_id)->first();
            if ($request->add_in_customer_page && empty($contact_exist)) {
                $contact = $this->createContact($request);
                CrmActivity::where('id', $id)->update('contact_id', $contact->id);
            }

            $reminder_data['name'] = $request->name;
            $reminder_data['options'] = 'when_login';

            Reminder::where('crm_reminder_id', $id)->update($reminder_data);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.crm_activity_update_success')
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
        try {
            CrmActivity::where('id', $id)->delete();
            CrmActivityDetail::where('crm_activity_details', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __("lang_v1.crm_activity_delete_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  $output;
    }
    /**
     * create contact
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createContact($request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
        }
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4|max:255',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => 'Password does not match'
            ];
            return $output;
        }

        $customer_data = array(
            'business_id' => $business_id,
            'first_name' => $request->name,
            'last_name' => '',
            'email' => $request->email,
            'username' => $request->contact_id,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'contact_number' => $request->alternate_number,
            'landline' => $request->landline,
            'geo_location' => '',
            'address' => $request->city,
            'town' => $request->district,
            'district' => $request->country,
            'is_company_customer' => 1
        );

        Customer::create($customer_data);


        $input = $request->only([
            'name', 'mobile', 'landline', 'alternate_number', 'city', 'country', 'contact_id', 'email'
        ]);
        $input['business_id'] = $business_id;
        $input['created_by'] = $request->session()->get('user.id');
        $input['type'] = 'customer';

        $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;

        //Check Contact id
        $count = 0;
        if (!empty($input['contact_id'])) {
            $count = Contact::where('business_id', $input['business_id'])
                ->where('contact_id', $input['contact_id'])
                ->count();
        }

        if ($count == 0) {
            //Update reference count
            $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

            if (empty($input['contact_id'])) {
                //Generate reference number
                $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
            }
            $contact = Contact::create($input);

            return $contact;
        } else {
            throw new \Exception("Error Processing Request", 1);
        }
    }
}
