<?php

namespace Modules\Ran\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\Product;
use App\Store;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldSmith;
use Modules\Ran\Entities\ReceiveWorkOrder;
use Modules\Ran\Entities\WorkOrder;
use Yajra\DataTables\Facades\DataTables;

class ReceiveWorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $receive_work_orders = ReceiveWorkOrder::where('receive_work_orders.business_id', $business_id)
                ->leftjoin('business_locations', 'receive_work_orders.location_id', 'business_locations.id')
                ->leftjoin('gold_smiths', 'receive_work_orders.goldsmith_id', 'gold_smiths.id')
                ->leftjoin('products', 'receive_work_orders.item_id', 'products.id')
                ->leftjoin('work_orders', 'receive_work_orders.work_order_id', 'work_orders.id')
                ->select([
                    'receive_work_orders.*',
                    'business_locations.name as business_location',
                    'gold_smiths.name as goldsmith',
                    'products.name as product',
                    'work_orders.work_order_no'
                ]);
            return DataTables::of($receive_work_orders)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\ReceiveWorkOrderController@edit', [$row->id]) . '" data-container=".production_modal" class="btn-modal receive_work_order__edit"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Ran\Http\Controllers\ReceiveWorkOrderController@destroy', [$row->id]) . '" class="delete-receive-work-order"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
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
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths =  GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $customers =  Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $products =  Product::where('business_id', $business_id)->pluck('name', 'id');
        $stores =  Store::where('business_id', $business_id)->pluck('name', 'id');
        $categories =  Category::where('business_id', $business_id)->pluck('name', 'id');

        $work_orders = WorkOrder::where('business_id', $business_id)->pluck('work_order_no', 'id');
        $work_order_id = ReceiveWorkOrder::where('business_id', $business_id)->count() + 1;


        return view('ran::production.receive_work_order.create')->with(compact(
            'business_locations',
            'goldsmiths',
            'work_order_id',
            'customers',
            'products',
            'stores',
            'work_orders',
            'categories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['created_by'] = Auth::user()->id;

            ReceiveWorkOrder::create($input);

            $output = [
                'success' => true,
                'tab' => 'receive',
                'msg' => __('ran::lang.receive_work_order_add_success')
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
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $receive_work_order = ReceiveWorkOrder::findOrFail($id);
        $work_order_items = WorkOrder::leftjoin('work_order_items', 'work_orders.id', 'work_order_items.work_order_id')
                ->where('work_orders.id', $receive_work_order->work_order_id)->select('work_order_items.item_id')->get()->toArray();

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths =  GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $customers =  Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $products =  Product::where('business_id', $business_id)->whereIn('id',$work_order_items)->pluck('name', 'id');
        $stores =  Store::where('business_id', $business_id)->pluck('name', 'id');
        $categories =  Category::where('business_id', $business_id)->pluck('name', 'id');

        $work_orders = WorkOrder::where('business_id', $business_id)->pluck('work_order_no', 'id');
        


        return view('ran::production.receive_work_order.edit')->with(compact(
            'business_locations',
            'goldsmiths',
            'customers',
            'products',
            'stores',
            'work_orders',
            'categories',
            'receive_work_order'
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
            $input = $request->except('_token', '_method');

            ReceiveWorkOrder::where('id', $id)->update($input);


            $output = [
                'success' => true,
                'tab' => 'receive',
                'msg' => __('ran::lang.receive_work_order_update_success')
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
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            ReceiveWorkOrder::where('id', $id)->delete();


            $output = [
                'success' => true,
                'msg' => __('ran::lang.receive_work_order_delete_success')
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
