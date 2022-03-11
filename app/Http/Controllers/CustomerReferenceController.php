<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS2D;
use Modules\Superadmin\Entities\HelpExplanation;
use Yajra\DataTables\DataTables;

class CustomerReferenceController extends Controller
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
            $query = CustomerReference::leftjoin('contacts AS cg', 'customer_references.contact_id', '=', 'cg.id')
                ->where('customer_references.business_id', $business_id)
                ->select([
                    'customer_references.*',
                    'cg.name as contact_name'
                ]);

            $customer_reference = Datatables::of($query)
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
                    @can("customer_reference.edit")
                    <li><a data-href="{{action(\'CustomerReferenceController@edit\', [$id])}}" class="btn-modal edit_reference_button" data-container=".customer_reference_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                    @endcan
                    <li><a href="{{action(\'CustomerReferenceController@destroy\', [$id])}}" class="delete_reference_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                    <li><a class="barcode_print" data-id="{{$id}}"><i class="glyphicon glyphicon-print"></i> @lang("messages.print")</a> </li>
                     </ul></div>'
                )
                ->editColumn('barcode_src', function ($row) {
                    return '<img class="barcode_show' . $row->id . '" style="max-width: 200px" src="' . $row->barcode_src . '" alt="barcode">';
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id');


            return $customer_reference->rawColumns(['action', 'barcode_src'])
                ->make(true);
        }
        return view('customer_reference.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $contacts = Contact::where('type', 'customer')->where('business_id', $business_id)->pluck('name', 'id');
        $help_explanations = HelpExplanation::pluck('value', 'help_key');
        $quick_add = !empty(request()->quick_add) ? request()->quick_add : 0;

        return view('customer_reference.create')->with(compact('contacts', 'help_explanations', 'quick_add'));
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
            $business_id = $request->session()->get('business.id');
            foreach ($request->ref as $ref) {
                $data = array(
                    'business_id' => $business_id,
                    'date' => date('Y-m-d', strtotime($ref['date'])),
                    'contact_id' => $ref['customer_id'],
                    'reference' => $ref['reference'],
                    'barcode_src' => $ref['barcode_src']
                );

                $customer_reference[] = CustomerReference::create($data);
            }
            $output = [
                'success' => 1,
                'msg' => __("lang_v1.contact_reference_create_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        if ($request->quick_add) {
            return $output;
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
        $business_id = request()->session()->get('business.id');
        $contacts = Contact::where('type', 'customer')->where('business_id', $business_id)->pluck('name', 'id');
        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $customer_reference = CustomerReference::findOrFail($id);

        return view('customer_reference.edit')->with(compact('contacts', 'help_explanations', 'customer_reference'));
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
                'contact_id' => $request->contact_id,
                'reference' => $request->reference,
                'barcode_src' => $request->barcode_src
            );

            CustomerReference::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __("lang_v1.contact_reference_update_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
            CustomerReference::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __("lang_v1.contact_reference_delete_success")
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

    public function getCustomerReference($id)
    {
        $refs = CustomerReference::where('contact_id', $id)->select('reference')->get();

        $html = '<option>Please Select</option>';

        foreach ($refs as $ref) {
            $html .= '<option value="' . $ref->reference . '">' . $ref->reference . '</option>';
        }

        return $html;
    }

    public function getCustomerReferenceBarcode(Request $request)
    {
        $customer_id = $request->customer_id;
        $reference = $request->reference;
        $html = '';
        if (!empty($customer_id) && !empty($reference)) {
            $customer = Contact::findOrFail($customer_id);
            $name = $customer->name;
            $barcode_string = $name . '.' . $reference;
            $qr = new DNS2D();
            $qr = $qr->getBarcodePNG($barcode_string, 'QRCODE');
            $barcode =  '<img style="max-width: 97%;" src="data:image/png;base64,' . $qr . '" alt="barcode"   />';

            $src = 'data:image/png;base64,' . $qr;

            return ['success' => 1, 'html' => $barcode, 'src' => $src];
        } else {
            return ['success' => 0, 'html' => $html];
        }
    }
}
