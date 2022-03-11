<?php

namespace Modules\Ran\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldSmith;
use Modules\Ran\Entities\WorkOrder;
use Modules\Ran\Entities\WorkOrderItem;
use Yajra\DataTables\Facades\DataTables;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $work_orders = WorkOrder::where('work_orders.business_id', $business_id)
                ->leftjoin('business_locations', 'work_orders.location_id', 'business_locations.id')
                ->leftjoin('gold_smiths', 'work_orders.goldsmith_id', 'gold_smiths.id')
                ->select([
                    'work_orders.*',
                    'business_locations.name as business_location',
                    'gold_smiths.name as goldsmith'
                ]);
            return DataTables::of($work_orders)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\WorkOrderController@edit', [$row->id]) . '" data-container=".production_modal" class="btn-modal work_order_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Ran\Http\Controllers\WorkOrderController@destroy', [$row->id]) . '" class="delete-work_order"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->addColumn('received_work_order_no', '')
                ->editColumn('order_delivery_date', '{{@format_date($order_delivery_date)}}')
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

        $work_order_id = WorkOrder::where('business_id', $business_id)->count() + 1;

        return view('ran::production.work_order.create')->with(compact(
            'business_locations',
            'goldsmiths',
            'work_order_id',
            'customers',
            'products'
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
            $input['business_id'] = $business_id;
            $input['date_and_time'] = $request->date_and_time;
            $input['location_id'] = $request->location_id;
            $input['goldsmith_id'] = $request->goldsmith_id;
            $input['work_order_no'] = $request->work_order_no;
            $input['order_delivery_date'] = !empty($request->order_delivery_date) ? Carbon::parse($request->order_delivery_date)->format('Y-m-d') : date('Y-m-d');
            $input['note'] = $request->note;
            $input['customer_order_no'] = $request->customer_order_no;
            $input['customer_id'] = $request->customer_id;
            $input['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $work_order = WorkOrder::create($input);
            $goldsmith = GoldSmith::findOrFail($input['goldsmith_id']);
            $balance = $goldsmith->opening_gold_qty;
            foreach ($request->list as $list) {
                $list['work_order_id'] = $work_order->id;
                $balance +=  $list['gold_qty'];
                WorkOrderItem::create($list);
            }

            $goldsmith->opening_gold_qty = $balance;
            $goldsmith->save();

            DB::commit();
            $output = [
                'success' => true,
                'tab' => 'work_order',
                'msg' => __('ran::lang.work_order_add_success')
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
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $goldsmiths =  GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $customers =  Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $products =  Product::where('business_id', $business_id)->pluck('name', 'id');

        $work_order = WorkOrder::findOrFail($id);
        $work_order_items = WorkOrderItem::where('work_order_id', $id)->get();

        return view('ran::production.work_order.edit')->with(compact(
            'business_locations',
            'goldsmiths',
            'work_order',
            'customers',
            'work_order_items',
            'products'
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
            $input['date_and_time'] = $request->date_and_time;
            $input['location_id'] = $request->location_id;
            $input['goldsmith_id'] = $request->goldsmith_id;
            $input['work_order_no'] = $request->work_order_no;
            $input['order_delivery_date'] = !empty($request->order_delivery_date) ? Carbon::parse($request->order_delivery_date)->format('Y-m-d') : date('Y-m-d');
            $input['note'] = $request->note;
            $input['customer_order_no'] = $request->customer_order_no;
            $input['customer_id'] = $request->customer_id;


            DB::beginTransaction();
            $work_order = WorkOrder::where('id', $id)->update($input);
            $goldsmith = GoldSmith::findOrFail($input['goldsmith_id']);
            $balance = $goldsmith->opening_gold_qty;

            foreach ($request->list as $list) {
                if (!empty($list['id'])) {
                    WorkOrderItem::where('id', $list['id'])->update($list);
                } else {
                    WorkOrderItem::create($list);
                    $balance +=  $list['gold_qty'];
                }
            }

            $goldsmith->opening_gold_qty = $balance;
            $goldsmith->save();

            $output = [
                'success' => true,
                'tab' => 'work_order',
                'msg' => __('ran::lang.work_order_update_success')
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
            WorkOrder::where('id', $id)->delete();


            $output = [
                'success' => true,
                'msg' => __('ran::lang.work_order_delete_success')
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
    public function deleteWorkItem()
    {
        try {
            $work_order_item = WorkOrderItem::where('id', request()->id)->first();
            $work_order = WorkOrder::where('id', $work_order_item->work_order_id)->first();
            $goldsmith = GoldSmith::findOrFail($work_order->goldsmith_id);
            $goldsmith->opening_gold_qty = $goldsmith->opening_gold_qty - $work_order_item->gold_qty;
            $goldsmith->save();
            $work_order_item->delete();
            $output = [
                'success' => true,
                'msg' => __('ran::lang.work_order_delete_success')
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

    public function getWorkOrderItems()
    {
        $items = WorkOrder::leftjoin('work_order_items', 'work_orders.id', 'work_order_items.work_order_id')
            ->leftjoin('products', 'work_order_items.item_id', 'products.id')
            ->where('work_orders.id', request()->work_order_id)->pluck('products.name', 'products.id');

        $output = '<option>Please Select </option>';

        foreach ($items as $key => $item) {
            $output .= '<option value="' . $key . '">' . $item . '</option>';
        }

        return $output;
    }

    public function getWorkOrderItemDetails()
    {
        $item = WorkOrder::leftjoin('work_order_items', 'work_orders.id', 'work_order_items.work_order_id')
            ->leftjoin('products', 'work_order_items.item_id', 'products.id')
            ->where('work_orders.id', request()->work_order_id)->where('work_order_items.item_id', request()->item_id)->first();

        return $item;
    }
}
