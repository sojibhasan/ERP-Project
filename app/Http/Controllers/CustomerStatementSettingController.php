<?php



namespace App\Http\Controllers;



use App\Contact;

use Illuminate\Http\Request;

use App\CustomerStatementSetting;

use Yajra\DataTables\Facades\DataTables;



class CustomerStatementSettingController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {

            $query = CustomerStatementSetting::leftjoin('contacts', 'customer_statement_settings.customer_id', 'contacts.id')

                ->where('customer_statement_settings.business_id', $business_id)

                ->select([

                    'customer_statement_settings.*',

                    'contacts.name as customer_name'

                ]);



            $fuel_tanks = DataTables::of($query)

                ->addColumn(

                    'action',

                    '<button data-href="{{action(\'CustomerStatementSettingController@edit\', [$id])}}" data-container=".customer_statement_modal" class="btn btn-primary btn-xs btn-modal edit_customer_statement_modal_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</button>'

                )

                ->removeColumn('id');





            return $fuel_tanks->rawColumns(['action'])

                ->make(true);

        }

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

        $business_id = request()->session()->get('business.id');

        try {



            $data = array(

                'business_id' => $business_id,

                'enable_separate_customer_statement_no' => $request->enable_separate_customer_statement_no,

                'customer_id' => $request->customer_id,

                'starting_no' => $request->starting_no,

            );



            CustomerStatementSetting::create($data);

            $output = [

                'success' => 1,

                'msg' => __('contact.separate_customer_statement_no_created')

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

        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');



        $setting = CustomerStatementSetting::findOrFail($id);



        return view('customer_statement.partials.settings_customer_statement_edit_modal')->with(compact('setting', 'customers'));

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

        $business_id = request()->session()->get('business.id');

        try {



            $data = array(

                'business_id' => $business_id,

                'enable_separate_customer_statement_no' => $request->enable_separate_customer_statement_no,

                'customer_id' => $request->customer_id,

                'starting_no' => $request->starting_no,

            );



            CustomerStatementSetting::where('id', $id)->update($data);

            $output = [

                'success' => 1,

                'msg' => __('contact.separate_customer_statement_no_created')

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

        //

    }

}

