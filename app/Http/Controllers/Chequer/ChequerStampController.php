<?php

namespace App\Http\Controllers\Chequer;

use App\Chequer\ChequerStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChequerStampController extends Controller
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
            $stamps = ChequerStamp::where('business_id', $business_id)
                ->select(
                    '*'
                )->groupBy('id');

            return Datatables::of($stamps)
                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a herf="#" data-href="' . action('Chequer\ChequerStampController@edit', [$row->id]) . '" class="btn-modal" data-container=".edit_modal"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                    
                    <li><a data-href="' . action('Chequer\ChequerStampController@destroy', [$row->id]) . '" class="delete_stamps"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> Delete</a></li>
                    ';
                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('stamp_image', function ($row) {
                    $image_url = asset('chequer/' . $row->stamp_image);
                    return "<img height='50' width='50' src='" . $image_url . "' />";
                })
                ->editColumn('active', function ($row) {
                    if ($row->stamp_status == 1) {
                        return "Yes";
                    } else {
                        return "No";
                    }
                })
                ->editColumn('updated_at', '{{date("Y-m-d", strtotime($updated_at))}}')
                ->rawColumns(['action', 'stamp_image', 'active'])
                ->make(true);
        }

        return view('chequer/stamps/index');
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
            $validate = Validator::make(
                $request->all(),
                [
                    'stamp_name' => 'required',
                    'upload_stamp' => 'required|mimes:jpeg,png,bmp|max:4096',
                ]
            );

            if ($validate->fails()) {
                $output = [
                    'success' => 0,
                    'msg' =>  __("messages.something_went_wrong")
                ];

                return  redirect()->back()->with('status', $output);
            }

            if (!file_exists('./public/chequer/stamps')) {
                mkdir('./public/chequer/stamps', 0777, true);
            }
            if ($request->hasfile('upload_stamp')) {
                $file = $request->file('upload_stamp');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/chequer/stamps', $filename);
                $uploadFileFicon = 'stamps/' . $filename;
            }

            $data = array(
                'business_id' => $business_id,
                'stamp_name' => $request->stamp_name,
                'stamp_image' => $uploadFileFicon,
                'stamp_entrydt' => date('Y-m-d'),
                'stamp_status' => !empty($request->active) ? 1 : 0

            );

            ChequerStamp::create($data);

            $output = [
                'success' => 1,
                'msg' => __("cheque.stamp_add_success")
            ];

            return  redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  redirect()->back()->with('status', $output);
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
        $stamp = ChequerStamp::where('id', $id)->first();

        return view('chequer/stamps/edit')->with(compact('stamp'));
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
            $validate = Validator::make(
                $request->all(),
                [
                    'stamp_name' => 'required',
                    'upload_stamp' => 'required|mimes:jpeg,png,bmp|max:4096',
                ]
            );

            if ($validate->fails()) {
                $output = [
                    'success' => 0,
                    'msg' =>  __("messages.something_went_wrong")
                ];

                return  redirect()->back()->with('status', $output);
            }

            if (!file_exists('./public/chequer/stamps')) {
                mkdir('./public/chequer/stamps', 0777, true);
            }
           

            $data = array(
                'business_id' => $business_id,
                'stamp_name' => $request->stamp_name,
                'stamp_status' => !empty($request->active) ? 1 : 0

            );


            ChequerStamp::where('id', $id)->update($data);

            if ($request->hasfile('upload_stamp')) {
                $file = $request->file('upload_stamp');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/chequer/stamps', $filename);
                $uploadFileFicon = 'stamps/' . $filename;

                ChequerStamp::where('id', $id)->update(['stamp_image' => $uploadFileFicon]);
            }
        
            $output = [
                'success' => 1,
                'msg' => __("cheque.stamp_add_success")
            ];

            return  redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  redirect()->back()->with('status', $output);
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
            ChequerStamp::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __("cheque.stamp_delete_success")
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
}
