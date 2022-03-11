<?php

namespace Modules\Property\Http\Controllers;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AjaxController extends Controller
{
    public function credit_sub_account_type_ajax(Request $request){
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $accounts = Account::where('id',$request->value)->pluck('name', 'id');
            return view('property::setting.payment_options.credit_sub_account_type_ajax')
                ->with(compact('accounts'));
        }
    }

}