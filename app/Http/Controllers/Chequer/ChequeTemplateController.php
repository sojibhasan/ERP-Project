<?php

namespace App\Http\Controllers\Chequer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Chequer\ChequeTemplate;
use App\System;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class ChequeTemplateController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
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
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $templates = ChequeTemplate::leftjoin('users', 'cheque_templates.created_by', 'users.id')
                ->where('cheque_templates.business_id', $business_id)
                ->select(
                    'cheque_templates.*',
                    'users.username'
                )->groupBy('id');

            return Datatables::of($templates)
                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="' . action('Chequer\ChequeTemplateController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                    
                    <li><a data-href="' . action('Chequer\ChequeTemplateController@destroy', [$row->id]) . '" class="delete_employee"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> Delete</a></li>
                    ';




                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('created_date', '{{date("Y-m-d", strtotime($created_date))}}')
                // ->rawColumns(['action'])
                ->make(true);
        }

        return view('chequer/templates/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $templates = ChequeTemplate::where('business_id', $business_id)->get();
        return view('chequer/templates/create')->with(compact('templates'));
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
            $temp_id = $request->temp_id;
            $data['business_id'] = $business_id;
            $data['template_name'] = $request->template_name;
            $data['template_size'] = $request->template_size;
            $data['seprator'] = $request->seprator;
            $data['words2'] = $request->words2;
            $data['words3'] = $request->words3;
            $data['pay_name'] = $request->pay_name;
            $data['date_pos'] = $request->date_pos;
            $data['date_format'] = $request->date_format;
            $data['amount'] = $request->amount;
            $data['amount_in_w1'] = $request->amount_in_words1;
            $data['amount_in_w2'] = $request->amount_in_words2;
            $data['amount_in_w3'] = $request->amount_in_words3;
            $data['template_cross'] = $request->cross;
            $data['pay_only'] = $request->pay_only;
            $data['not_negotiable'] = $request->negotiable;
            $data['signature_stamp'] = $request->signature_stamp;
            $data['is_dublecross'] = $request->is_dublecross;
            $data['dublecross'] = $request->dublecross;
            $data['strikeBearer'] = $request->strikeBearer;
            $data['is_strikeBearer'] = $request->is_strikeBearer;
            $data['is_stamp'] = $request->is_stamp;
            $data['d1'] = $request->d1;
            $data['d2'] = $request->d2;
            $data['m1'] = $request->m1;
            $data['m2'] = $request->m2;
            $data['y1'] = $request->y1;
            $data['y2'] = $request->y2;
            $data['y3'] = $request->y3;
            $data['y4'] = $request->y4;
            $data['ds1'] = $request->ds1;
            $data['ds2'] = $request->ds2;
            $data['signature_stamp_area'] = $request->signature_stamp_area;
            $data['created_by'] = Auth::user()->id;
            $data['created_date'] = date('Y-m-d H:i:s', time());
            if ($temp_id) {
                if ($request->image_url) {
                    $data['image_url'] = $request->image_url;
                }
                ChequeTemplate::where('id', $temp_id)->update($data);
            } else {
                $data['image_url'] = $request->image_url;
                ChequeTemplate::create($data);
            }

            $output = [
                'success' => 1,
                'msg' => __('cheque.template_add_succuss')
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
        $templates = ChequeTemplate::where('business_id', $business_id)->get();
        return view('chequer/templates/create')->with(compact('templates', 'id'));
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
        //to update template store method is use
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
            ChequeTemplate::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __("cheque.template_delete_success")
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

    public function getTemplateValues()
    {
        $id = request()->id;
        $values = ChequeTemplate::where('id', $id)->first();
        echo json_encode($values);
    }

    public function uploadImageFile(Request $request)
    {

        //upload prfile image
        if (!file_exists('./public/chequer/uploads')) {
            mkdir('./public/chequer/uploads', 0777, true);
        }
        try {
            if ($request->hasfile('userfile')) {

                $validate = Validator::make(
                    $request->all(),
                    [
                        'userfile' => 'mimes:jpeg,png,bmp|max:4096',
                    ]
                );

                if ($validate->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => 'Only jpeg, png, bmp are allowed.'
                    ];

                    return  $output;
                }

                if ($request->hasfile('userfile')) {
                    $file = $request->file('userfile');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('public/chequer/uploads', $filename);
                }
            }
            $output = [
                'success' => 1,
                'msg' => __("cheque.file_upload_success"),
                'filename' => $filename
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }
}
