<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Property\Entities\FinanceOption;
use Modules\Property\Entities\InstallmentCycle;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyBlock;
use Modules\Property\Entities\SalesOfficer;
use Yajra\DataTables\Facades\DataTables;

class PriceChangesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $priceChanges = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
                ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
                ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as commission_approved_by', 'property_blocks.commission_approved_by', '=', 'commission_approved_by.id')
                ->leftJoin('property_account_settings', 'properties.id', '=', 'property_account_settings.property_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'property_sell')
                ->select(
                    'properties.id as property_id',
                    'properties.name as property_name',
                    'property_blocks.block_number as block_number',
                    'property_blocks.block_sale_price as sale_price',
                    'property_blocks.block_sold_price as sold_price',
                    'property_blocks.sale_commission as commission',
                    'property_blocks.commission_approval as commission_approval',
                    'property_blocks.commission_status as commission_status',
                    'transactions.id',
                    'transactions.transaction_date',
                    DB::raw("CONCAT(COALESCE(commission_approved_by.surname, ''),' ',COALESCE(commission_approved_by.first_name, ''),' ',COALESCE(commission_approved_by.last_name,'')) as commission_approved_by"),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $priceChanges->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!is_null(request()->project_id) && !empty(request()->project_id)) {
                $priceChanges->where('properties.id', request()->project_id);
            }

            if (!is_null(request()->location_id) && !empty(request()->location_id)) {
                $priceChanges->where('transactions.location_id', request()->location_id);
            }

            if (!is_null(request()->block_id) && !empty(request()->block_id)) {
                $priceChanges->where('property_blocks.id', request()->block_id);
            }

            if (!is_null(request()->sales_commission_status) && !empty(request()->sales_commission_status)) {
                $priceChanges->where('property_blocks.commission_status', request()->sales_commission_status);
            }

            if (!is_null(request()->commission_entered_by) && !empty(request()->commission_entered_by)) {
                $priceChanges->where('property_blocks.commission_entered_by', request()->commission_entered_by);
            }

            if (!is_null(request()->approved_by) && !empty(request()->approved_by)) {
                $priceChanges->where('property_blocks.commission_approved_by', request()->approved_by);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $priceChanges->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            return DataTables::of($priceChanges)
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'sale_price',
                    '<span class="display_currency sale_price" data-currency_symbol="true" data-orig-value="{{$sale_price}}">{{$sale_price}}</span>'
                )
                ->editColumn(
                    'sold_price',
                    '<span class="display_currency sold_price" data-currency_symbol="true" data-orig-value="{{$sold_price}}">{{$sold_price}}</span>'
                )
                ->addColumn('changed_amount', function ($row) {
                    $changed_amount = $row->sold_price - $row->sale_price;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $changed_amount . '">' . $changed_amount . '</span>';
                })
                ->editColumn('sold_by', function ($row) {
                    $block_count = PropertyBlock::where('property_id', $row->property_id)->first();
                    if(!empty($block_count )){
                        $user = User::where('id', $block_count->sold_by)->first();
                        if (!empty($user)) {
                            return $user->first_name . ' ' . $user->last_name;
                        }
                    }
                    return '';
                })
                ->editColumn('commission', function ($row) {
                    $html = '<span>'.$row->commission.'</span>';
                    if(request()->user()->can('property.update_sale_commission')) {
                        $html .= '<a href="javascript:void(0)" class="edit-commission" data-property-block-id="' . $row->property_block_id . '" 
                            data-commission="'. $row->commission .'"><i class="fa fa-edit" aria-hidden="true" ></i></a>';
                    }
                    return $html;
                })
                ->editColumn('commission_approval', function ($row) {
                    if($row->commission_approval === 'pending') {
                        if(request()->user()->can('property.approve_commission')) {
                            $html = '<button class="btn btn-sm btn-danger approve-commission-btn" data-property-block-id="'.$row->property_block_id.'">'. __('property::lang.pending') .'</button>';
                        } else {
                            $html = __('property::lang.pending');
                        }
                    } else {
                        $html = $row->commission_approved_by;
                    }
                    return $html;
                })
                ->editColumn('commission_status', function ($row) {
                    if(request()->user()->can('property.update_commission_status')) {
                        $html = '<button class="btn btn-sm ' . ($row->commission_status == 'pending' ? 'btn-danger' : 'btn-success') . ' commission-status-toggle-btn" data-property-block-id="' . $row->property_block_id . '" 
                            data-commission-status="' . $row->commission_status . '">' . ($row->commission_status == 'pending' ? __('property::lang.pending') : __('property::lang.paid')) . '</button>';
                    } else {
                        $html = ($row->commission_status == 'pending' ? __('property::lang.pending') : __('property::lang.paid'));
                    }
                    return $html;
                })
                ->rawColumns(['transaction_date', 'sale_price', 'sold_price', 'changed_amount', 'sold_by', 'commission', 'commission_approval', 'commission_status'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $projects = Property::where('business_id', $business_id)->pluck('name', 'id');
        $sale_officers = SalesOfficer::leftjoin('users', 'sales_officers.officer_id', '=', 'users.id')->where('sales_officers.business_id', $business_id)->pluck('username', 'sales_officers.id');
        $blocks = Property::getLandAndBlockDropdown($business_id, true, true);
        $sales_commission_status = [
            'pending' => __('property::lang.pending'),
            'paid' => __('property::lang.paid')
        ];
        $users = User::select(DB::RAW("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as username"), 'id')->pluck('username', 'id');

        return view('property::price_changes.index')
            ->with(compact(
                'business_locations',
                'projects',
                'sale_officers',
                'blocks',
                'sales_commission_status',
                'users'
            ));
    }

    /**
     * Update property block commission
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCommission(Request $request, $id)
    {
        if(!request()->user()->can('property.update_sale_commission')) {
            abort(404);
        }

        $input = $request->validate([
            'sale_commission' => 'required|numeric'
        ]);

        $input['commission_entered_by'] = $request->user()->id;

        PropertyBlock::findOrFail($id)->update($input);

        return response()->json(['success' => true], 200);
    }

    /**
     * Approve property block commission
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveCommission(Request $request, $id)
    {
        if(!request()->user()->can('property.approve_commission')) {
            abort(404);
        }

        $input = [
            'commission_approval' => 'approved',
            'commission_approved_by' => $request->user()->id
        ];
        PropertyBlock::findOrFail($id)->update($input);

        return response()->json(['success' => true], 200);
    }

    /**
     * Update property block commission status
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCommissionStatus(Request $request, $id)
    {
        if(!request()->user()->can('property.update_commission_status')) {
            abort(404);
        }

        $propertyBlock = PropertyBlock::findOrFail($id);

        $input = [
            'commission_status' => $propertyBlock->commission_status === 'pending' ? 'paid' : 'pending',
            'commission_status_updated_by' => $request->user()->id
        ];

        $propertyBlock->update($input);

        return response()->json(['success' => true], 200);
    }
}
