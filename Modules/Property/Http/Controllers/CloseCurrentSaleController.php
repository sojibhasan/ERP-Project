<?php

namespace Modules\Property\Http\Controllers;

use App\Transaction;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\BlockCloseReason;
use Modules\Property\Entities\CloseCurrentSale;
use Modules\Property\Entities\FinanceOption;
use Modules\Property\Entities\Installment;
use Modules\Property\Entities\InstallmentCycle;
use Modules\Property\Entities\PropertyBlock;
use Modules\Property\Entities\PropertyFinalize;

class CloseCurrentSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('property::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $id = request()->finalize_id;
        $property_finalize = PropertyFinalize::find($id);
        $sell_line_id = $property_finalize->property_sell_line_id;
        $business_id = request()->session()->get('user.business_id');
        $property_sell = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('property_sell_lines.id', $sell_line_id)
            ->select(
                'property_blocks.*',
                'properties.name as property_name',
                'business_locations.name as location_name',
                'contacts.name as customer_name',
                'contacts.contact_id',
                'transactions.transaction_date',
                'transactions.invoice_no',
                'property_sell_lines.id as property_sell_line_id',
                'transactions.id as transaction_id',
                DB::raw('SUM(transaction_payments.amount) as total_amount_paid')
            )

            ->first();

        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');
        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');
        $installments = Installment::where('transaction_id', $property_finalize->transaction_id)->get();
        $finance_option = FinanceOption::find($property_finalize->finance_option_id);
        $reasons = BlockCloseReason::where('business_id', $business_id)->pluck('reason', 'id');

        return view('property::close_current_sale.create')->with(compact(
            'property_sell',
            'finance_options',
            'finance_option',
            'installment_cycles',
            'installments',
            'reasons',
            'property_finalize'
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
            $id = $request->finalize_id;

            $business_id = request()->session()->get('user.business_id');
            $data = [
                'business_id' => $business_id,
                'property_finalize_id' => $id,
                'is_closed' => 1,
                'closed_by' => Auth::user()->id,
                'reason_id' => !empty($request->reason_id) ? $request->reason_id : [],
                'all_payments_completed' => !empty($request->all_payments_completed) ? 1 : 0
            ];
            DB::beginTransaction();
            $close_current_sale = CloseCurrentSale::create($data);


            $property_finalize = PropertyFinalize::find($id);

            $property_finalize->is_closed = 1;
            $property_finalize->closed_by = Auth::user()->id;
            $property_finalize->reason_id = !empty($request->reason_id) ? $request->reason_id : [];
            $property_finalize->all_payments_completed = !empty($request->all_payments_completed) ? 1 : 0;
            $property_finalize->save();

            $close_current_sale->block_id = $property_finalize->block_id;
            $close_current_sale->property_id = $property_finalize->property_id;
            $close_current_sale->transaction_id = $property_finalize->transaction_id;
            $close_current_sale->save();

            //update block status values
            $block = PropertyBlock::find($property_finalize->block_id);
            $block->is_sold = !empty($request->all_payments_completed) ? 0 : $block->is_sold; // if payment not completed then make available block for sell
            $block->is_closed = 1;
            $block->all_payments_completed = !empty($request->all_payments_completed) ? 1 : 0;
            $block->save();
            DB::commit();

            $output = [
                'success' => true,
                'tab' => 'property_details',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'property_details',
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
        $business_id = request()->session()->get('user.business_id');
        $closed_current_sales = CloseCurrentSale::where('property_finalize_id', $id)
            ->leftjoin('transactions', 'close_current_sales.transaction_id', 'transactions.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('users', 'close_current_sales.closed_by', 'users.id')
            ->select('close_current_sales.*', 'transactions.transaction_date', 'contacts.name as customer_name', 'users.username')
            ->with('property_finalize')
            ->get();


        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');
        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::close_current_sale.show')->with(compact(
            'closed_current_sales',
            'finance_options',
            'installment_cycles'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $property_finalize = PropertyFinalize::find($id);
        $sell_line_id = $property_finalize->property_sell_line_id;
        $business_id = request()->session()->get('user.business_id');
        $property_sell = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('property_sell_lines.id', $sell_line_id)
            ->select(
                'property_blocks.*',
                'properties.name as property_name',
                'business_locations.name as location_name',
                'contacts.name as customer_name',
                'contacts.contact_id',
                'transactions.transaction_date',
                'transactions.invoice_no',
                'property_sell_lines.id as property_sell_line_id',
                'transactions.id as transaction_id',
                DB::raw('SUM(transaction_payments.amount) as total_amount_paid')
            )

            ->first();

        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');
        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');
        $installments = Installment::where('transaction_id', $property_finalize->transaction_id)->get();
        $finance_option = FinanceOption::find($property_finalize->finance_option_id);
        $reasons = BlockCloseReason::where('business_id', $business_id)->pluck('reason', 'id');

        return view('property::close_current_sale.edit')->with(compact(
            'property_sell',
            'finance_options',
            'finance_option',
            'installment_cycles',
            'installments',
            'reasons',
            'property_finalize'
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
            $property_finalize = PropertyFinalize::find($id);
            $data = [
                'is_closed' => 1,
                'closed_by' => Auth::user()->id,
                'reason_id' => !empty($request->reason_id) ? $request->reason_id : [],
                'all_payments_completed' => !empty($request->all_payments_completed) ? 1 : 0
            ];
            DB::beginTransaction();
            $close_current_sale = CloseCurrentSale::where('property_finalize_id', $id)->orderBy('id', 'desc')->first();
            $close_current_sale->update($data);

            $property_finalize->is_closed = 1;
            $property_finalize->closed_by = Auth::user()->id;
            $property_finalize->reason_id = !empty($request->reason_id) ? $request->reason_id : [];
            $property_finalize->all_payments_completed = !empty($request->all_payments_completed) ? 1 : 0;
            $property_finalize->save();

            //update block status values
            $block = PropertyBlock::find($property_finalize->block_id);
            $block->is_sold = !empty($request->all_payments_completed) ? 0 : $block->is_sold; // if payment not completed then make available block for sell
            $block->is_closed = 1;
            $block->all_payments_completed = !empty($request->all_payments_completed) ? 1 : 0;
            $block->save();

            $output = [
                'success' => true,
                'tab' => 'property_details',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'property_details',
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
        //
    }
}
