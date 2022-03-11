<?php

namespace Modules\HR\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\NoticeBoard;
use Yajra\DataTables\Facades\DataTables;

class NoticeBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $notice_boards = NoticeBoard::where('notice_boards.business_id', $business_id)
                ->select([
                    'notice_boards.*'
                ]);


            $notice_boards->groupBy('notice_boards.id');



            return DataTables::of($notice_boards)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\NoticeBoardController@edit', [$row->id]) . '" data-container=".notice_model" class="btn-modal notice_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\NoticeBoardController@destroy', [$row->id]) . '" class="delete-notice"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('status', '{{ucfirst($status)}}')
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::notice.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::notice.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['date'] = Carbon::now();
            NoticeBoard::create($input);

            $output = [
                'success' => true,
                'msg' => __('hr::lang.notice_create_success')
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
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        return view('hr::notice.edit')->with(compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token', '_method');
            NoticeBoard::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __('hr::lang.notice_update_success')
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
     * @return Response
     */
    public function destroy($id)
    {
        try {
            NoticeBoard::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.notice_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
