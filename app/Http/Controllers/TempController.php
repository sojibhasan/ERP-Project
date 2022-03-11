<?php

namespace App\Http\Controllers;

use App\TempData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TempController extends Controller
{
    public function saveAddSaleTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['sale_create_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveAddPurchaseTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['pos_create_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveSaleReturnTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['sale_return_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveStockTransferTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['stock_transfer_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveStockAdjustmentTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['stock_adjustment_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveAddExpenseTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['add_expense_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function saveAddPosTemp(Request $request){
        $business_id = request()->session()->get('user.business_id');
        TempData::updateOrCreate(['business_id' => $business_id],['add_pos_data' => json_encode($request->except('_token'))]);
        return json_encode(['status' => true]);
    }

    public function check_temp_data_for_business($business_id){
        $data = TempData::where('business_id', $business_id)->first();

        if(empty($data)){
            $new_data = array(
                'business_id' => $business_id,
            );
            TempData::insert($new_data);
        }
        return true;
    }

    public function clearData($type){
        TempData::where('business_id', request()->session()->get('user.business_id'))->update([$type => '']);
        return redirect()->back();
    }
}
