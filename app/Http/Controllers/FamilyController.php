<?php

namespace App\Http\Controllers;

use App\PatientDetail;
use Illuminate\Http\Request;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\Package;

class FamilyController extends Controller
{

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $subscription = Subscription::active_subscription($business_id);
        $max_family_member = 0;
        if (!empty($subscription)) {
            $pacakge_details = $subscription->package_details;
            $max_family_member = $pacakge_details['user_count'];
        } 
        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $users = User::where('business_id', $business_id)
                ->where('id', '!=', $user_id)
                ->where('is_cmmsn_agnt', 0)
                ->select([
                    'id', 'username',
                    DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email'
                ]);

            return Datatables::of($users)
                ->addColumn(
                    'role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);
                        return $role_name;
                    }
                )
                ->addColumn(
                    'action',
                    '@can("user.update")
                        <a href="{{action(\'PatientController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    @endcan
                    @can("user.view")
                   <!-- <a href="{{action(\'FamilyController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a> -->
                    &nbsp;
                    @endcan
                    @can("user.delete")
                        <button data-href="{{action(\'FamilyController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }


        return view('family_members.index')->with(compact('max_family_member'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
        }




        return view('family_members.create')
            ->with(compact('business_id'));
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
            //Create owner.
            $owner_details = $request->only(['email', 'p_password', 'language']);
            $owner_details['password'] = $owner_details['p_password'];
            $owner_details['language'] = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            $owner_details['surname'] = '';
            $owner_details['first_name'] = $request->name;
            $owner_details['last_name'] = '';
            $owner_details['username'] =  $request->patient_id;
            $user = User::create_user($owner_details);


            $data_details = array(
                'user_id'  => $user->id,
                'name'  => $request->name,
                'address'  => $request->address,
                'country'  => $request->country,
                'state'  => $request->state,
                'city'  => $request->city,
                'mobile'  => $request->mobile,
                'date_of_birth'  => $request->date_of_birth,
                'gender'  => $request->gender,
                'marital_status'  => $request->marital_status,
                'blood_group'  => $request->blood_group,
                'height'  => $request->height,
                'weight'  => $request->weight,
                'guardian_name'  => $request->guardian_name,
                'time_zone'  => $request->time_zone,
                'known_allergies'  => $request->known_allergies,
                'notes'  => $request->notes
            );
            //upload prfile image
            if (!file_exists('./public/img/patient_photos')) {
                mkdir('./public/img/patient_photos', 0777, true);
            }
            if ($request->hasfile('fileToImage')) {
                $file = $request->file('fileToImage');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/patient_photos', $filename);
                $uploadFileFicon = 'public/img/patient_photos/' . $filename;
                $data_details['profile_image'] = $uploadFileFicon;
            } else {
                $data_details['profile_image'] = '';
            }

            $patient_details = PatientDetail::create($data_details);

            //Update user with business id
            $user->business_id = $request->session()->get('business.id');
            $user->save();

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __("patient.family_member_added")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
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
