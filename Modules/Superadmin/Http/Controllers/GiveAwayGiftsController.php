<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\GiveAwayGift;
use Yajra\DataTables\Facades\DataTables;

class GiveAwayGiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $give_away_gifts = GiveAwayGift::all();

            return DataTables::of($give_away_gifts)
                ->addColumn('action', function ($row) {

                    $action = '';
                    $action .= '<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="' . action('\Modules\Superadmin\Http\Controllers\GiveAwayGiftsController@edit', [$row->id]) . '" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</button>';
                    $action .= '&nbsp <button data-href="' . action('\Modules\Superadmin\Http\Controllers\GiveAwayGiftsController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_give_away_gift_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';


                    return $action;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('superadmin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $input = $request->except('_toekn');
            GiveAwayGift::create($input);
            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.success')
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

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $gift = GiveAwayGift::find($id);

        return view('superadmin::superadmin_settings.give_away_gifts.edit')->with(compact(
            'gift'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $input['name'] = $request->name;
            GiveAwayGift::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.success')
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

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            GiveAwayGift::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.success')
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
