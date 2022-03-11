<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Crm;
use App\CrmComment;
use App\CrmGroup;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CRMController extends Controller
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
            return $this->indexCrm();
        }

        $type = 'crm';
        return view('crm.index')
            ->with(compact('type'));
    }


    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCrm()
    {
        if (!auth()->user()->can('crm.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = Crm::leftjoin('crm_groups AS cg', 'crms.crm_group_id', '=', 'cg.id')
            ->where('crms.business_id', $business_id)
            ->select([
                'crms.id', 'crms.business_name',
                'contact_id', 'crms.created_at', 'total_rp', 'cg.name as crm_group', 'city', 'district', 'country', 'mobile',  'is_default',
            ])
            ->groupBy('crms.id');

        $contacts = Datatables::of($query)
            ->addColumn('address', '{{implode(array_filter([$city, $district, $country]), ", ")}}')

            ->addColumn(
                'action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                @can("crm.view")
                <li><a class="" href="{{action(\'CRMController@show\', $id)}}">
                <i class="fa fa-eye"></i> @lang("messages.view")</a> </li>
                   
                @endcan
                @can("crm.update")
                <li><a class=" btn-modal" data-href="{{action(\'CRMController@edit\', $id)}}"
                data-container=".crm_edit">
                <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a> </li>
                
                @endcan
                @can("crm.delete")
                    <li><a href="{{action(\'CRMController@destroy\', [$id])}}" class="delete_crm_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                @endcan </ul></div>'
            )
            ->addColumn(
                'town',
                '{{$city}}'
            )
            ->addColumn(
                'user',
                '{{Auth::User()->username}}'
            )
            ->editColumn('created_at', '{{@format_date($created_at)}}')
            ->removeColumn('country')
            ->removeColumn('id');


        return $contacts->rawColumns(['address', 'action'])
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('crm.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $crm_groups = CrmGroup::forDropdown($business_id);

        return view('crm.create')
            ->with(compact('types', 'crm_groups'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crm.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }

            $input = $request->only([
                'business_name', 'mobile', 'landline', 'alternate_number', 'city', 'district', 'country', 'landmark', 'crm_group_id', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email'
            ]);
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            //Check Contact id
            $count = 0;
            if (!empty($input['contact_id'])) {
                $count = Crm::where('business_id', $input['business_id'])
                    ->where('contact_id', $input['contact_id'])
                    ->count();
            }

            if ($count == 0) {

                $contact = Crm::create($input);

                $output = [
                    'success' => true,
                    'data' => $contact,
                    'msg' => __("lang_v1.added_success")
                ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with(compact('output'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (!auth()->user()->can('crm.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $comments = CrmComment::where('crm_id', $id)
                ->select('comment_date', 'comments', 'next_follow_up');

            return Datatables::of($comments)
      
                ->editColumn('comment_date', '{{$comment_date}}' )
                ->editColumn('comments', '{{$comments}}' )
                ->editColumn('next_follow_up', '{{$next_follow_up}}')
                ->removeColumn('id')
                ->make(true);
        }


        $crm = Crm::where('id', $id)->first();
        $crm_groups = CrmGroup::forDropdown($business_id);
        return view('crm.show')->with(compact('crm', 'crm_groups'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        if (!auth()->user()->can('crm.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if (!auth()->user()->can('crm.view')) {
            abort(403, 'Unauthorized action.');
        }

        $crm = Crm::where('id', $id)->first();
        $crm_groups = CrmGroup::forDropdown($business_id);
        return view('crm.edit')->with(compact('crm', 'crm_groups'));
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
        if (!auth()->user()->can('crm.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'business_name', 'mobile', 'landline', 'alternate_number', 'city', 'district', 'country', 'landmark', 'crm_group_id', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email'
            ]);
            $business_id = $request->session()->get('user.business_id');

            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            Crm::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __("lang_v1.crm_update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with(compact('output'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('crm.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            Crm::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __("lang_v1.crm_update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  $output;
    }


    /**
     * Add comment for crm
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addComments(Request $request)
    {
        if (!auth()->user()->can('crm.add_comments')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'comment_date' => 'required',
            'comments' => 'required|max:255|string',
            'next_follow_up' => 'required',
            'crm_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];

            return $output;
        }

        try {
            $data = [
                'comment_date' => $request->input('comment_date'),
                'comments' => $request->input('comments'),
                'next_follow_up' => date('Y-m-d', strtotime($request->input('next_follow_up'))),
                'crm_id' => $request->input('crm_id'),
                'user_id' => $request->input('user_id'),
            ];
            CrmComment::insert($data);

            $output = [
                'success' => true,
                'msg' => __("lang_v1.comment_add_success")
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
            return $output;
        }
    }


    
}
