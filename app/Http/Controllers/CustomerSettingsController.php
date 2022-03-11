<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Events\CustomerLimitApproval;
use Composer\Semver\Comparator;
use Illuminate\Http\Request;
use Illuminate\Routing\RedirectController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CustomerSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = Contact::where('contacts.business_id', $business_id)
                ->onlyCustomers()
                ->select([
                    'contacts.contact_id', 'contacts.name as customer_name', 'contacts.credit_limit', 'contacts.id'

                ])
                ->groupBy('contacts.id');

            $contacts = DataTables::of($query)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
              
                    <li><a data-href="{{action(\'CustomerSettingsController@edit\', [$id])}}?type=sell" class="btn-modal" data-container=".customer_settings_model"><i class="fa fa-credit-card" aria-hidden="true"></i>@lang("lang_v1.default_settings_for_customer_credit_limit")</a></li>
                </ul></div>'
                )
                ->editColumn('credit_limit', function ($row) {
                    $html = __('lang_v1.no_limit');
                    if (!is_null($row->credit_limit)) {
                        $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->credit_limit . '">' . $row->credit_limit . '</span>';
                    }

                    return $html;
                });

            return $contacts->rawColumns(['action', 'credit_limit'])
                ->make(true);
        }

        return view('customer_settings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $contact = Contact::findOrFail($id);

        return view('customer_settings.edit')->with(compact('contact'));
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
            $data = $request->except('_token', '_method');
            Contact::where('id', $id)->update($data);

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
