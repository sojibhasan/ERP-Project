<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\Business;
use Illuminate\Http\Request;
use App\DefaultAccount;
use App\DefaultAccountType;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DefaultAccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->get();

        return view('default_account.create_account_type')
            ->with(compact('account_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only(['name', 'parent_account_type_id']);
            $input['business_id'] = $request->session()->get('user.business_id');

            $account_type = DefaultAccountType::create($input);


            //adding account for other businesses
            $businesses = Business::all();
            foreach ($businesses as $key => $value) {
                $p_acc_type_name =  DefaultAccountType::where('id', $account_type->parent_account_type_id)->first();
                if (empty($p_acc_type_name)) {
                    $parent_account_type_id = null;
                } else {
                    $parent_account_type_id = AccountType::where('business_id', $value->id)->where('name', $p_acc_type_name->name)->first();
                }
                $data = array(
                    'business_id' => $value->id,
                    'name' => $account_type->name,
                    'parent_account_type_id' => !empty($parent_account_type_id) ? $parent_account_type_id->id : null,
                    'default_account_type_id' => $account_type->id,
                );
                $account_type_exist = AccountType::where('business_id', $value->id)->where('name',  $account_type->name)->first();
                if (empty($account_type_exist)) {
                    AccountType::create($data);
                }
            }


            $output = [
                'success' => true,
                'msg' => __("lang_v1.added_success"),
                'account_default' => true
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'account_default' => true
            ];
        }

        return redirect()->back()->with('status', $output);
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

        $business_id = session()->get('user.business_id');

        $account_type = DefaultAccountType::where('business_id', $business_id)
            ->findOrFail($id);

        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->get();

        return view('default_account.edit_account_type')
            ->with(compact('account_types', 'account_type'));
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
        try {
            $input = $request->only(['name', 'parent_account_type_id']);
            $business_id = $request->session()->get('user.business_id');

            $account_type = DefaultAccountType::where('business_id', $business_id)
                ->findOrFail($id);

            //Account type is changed to subtype update all its sub type's parent type
            if (empty($account_type->parent_account_type_id) && !empty($input['parent_account_type_id'])) {
                DefaultAccountType::where('business_id', $business_id)
                    ->where('parent_account_type_id', $account_type->id)
                    ->update(['parent_account_type_id' => $input['parent_account_type_id']]);
            }



            //updating account for other businesses
            $businesses = Business::all();
            foreach ($businesses as $key => $value) {
                $business_acc_type =  AccountType::where('default_account_type_id', $account_type->id)->where('business_id', $value->id)->first();
                $p_acc_type_id =   AccountType::where('default_account_type_id', $input['parent_account_type_id'])->where('business_id', $value->id)->first();
                if (!empty($business_acc_type)) {
                    if (empty($p_acc_type_id)) {
                        $parent_account_type_id = null;
                    } else {
                        $parent_account_type_id =  $p_acc_type_id->id;
                    }
                    $data = array(
                        'name' => $input['name'],
                        'parent_account_type_id' => $parent_account_type_id,
                        'default_account_type_id' => $account_type->id,
                    );
                    AccountType::where('id', $business_acc_type->id)->update($data);
                }
            }

            $account_type->update($input);


            $output = [
                'success' => true,
                'msg' => __("lang_v1.updated_success"),
                'account_default' => true
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'account_default' => true
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_id = session()->get('user.business_id');

        DefaultAccountType::where('business_id', $business_id)
            ->where('id', $id)
            ->delete();

        //Upadete parent account if set
        DefaultAccountType::where('business_id', $business_id)
            ->where('parent_account_type_id', $id)
            ->update(['parent_account_type_id' => null]);

        //deleting account for other businesses
        $businesses = Business::all();
        foreach ($businesses as $key => $value) {
            $business_acc_type =  AccountType::where('default_account_type_id', $id)->where('business_id', $value->id)->first();
            if (!empty($business_acc_type)) {
                $parent_id = $business_acc_type->id;
                AccountType::where('business_id', $value->id)
                    ->where('parent_account_type_id', $parent_id)
                    ->update(['parent_account_type_id' => null]);
                $business_acc_type->delete();
            }
        }

        $output = [
            'success' => true,
            'msg' => __("lang_v1.deleted_success")
        ];

        return redirect()->back()->with('status', $output);
    }
}
