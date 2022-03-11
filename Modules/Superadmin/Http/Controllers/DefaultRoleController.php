<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\SellingPriceGroup;
use App\Utils\ModuleUtil;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;


class DefaultRoleController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
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
        if (!auth()->user()->can('roles.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $roles = Role::where('business_id', $business_id)->where('is_superadmin_default', 1)
                ->select(['name', 'id', 'is_default', 'business_id']);

            return DataTables::of($roles)
                ->addColumn('action', function ($row) {
                    if (!$row->is_default || $row->name == "Cashier#" . $row->business_id) {
                        $action = '';
                        if (auth()->user()->can('roles.update')) {
                            $action .= '<a href="' . action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@edit', [$row->id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';
                        }
                        if (auth()->user()->can('roles.delete')) {
                            $action .= '&nbsp
                                <button data-href="' . action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_role_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                        }

                        return $action;
                    } else {
                        return '';
                    }
                })
                ->editColumn('name', function ($row) use ($business_id) {
                    $role_name = str_replace('#' . $business_id, '', $row->name);
                    if (in_array($role_name, ['Admin', 'Cashier'])) {
                        $role_name = __('lang_v1.' . $role_name);
                    }
                    return $role_name;
                })
                ->removeColumn('id')
                ->removeColumn('is_default')
                ->removeColumn('business_id')
                ->rawColumns([1])
                ->make(false);
        }

        return view('superadmin::superadmin_settings.role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->get();

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');

        $get_permissions['mpcs_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mpcs_module');
        $get_permissions['enable_cheque_writing'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_cheque_writing');
        $get_permissions['enable_petro_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $get_permissions['issue_customer_bill'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'issue_customer_bill');
        $get_permissions['hr_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_module');
        $get_permissions['tasks_management'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tasks_management');
        $get_permissions['member_registration'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');
        $get_permissions['upload_images'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'upload_images');
        $get_permissions['leads_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_module');
        $get_permissions['sms_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sms_module');
        $get_permissions['customer_settings'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'customer_settings');
        $get_permissions['repair_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module');
        $get_permissions['mf_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module');
        $get_permissions['cache_clear'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'cache_clear');
        $get_permissions['sms_enable'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sms_enable');
        $get_permissions['enable_restaurant'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_restaurant');
        $get_permissions['ran_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'ran_module');

        return view('superadmin::superadmin_settings.role.create')
            ->with(compact('selling_price_groups', 'module_permissions', 'get_permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $role_name = $request->input('name');
            $permissions = $request->input('permissions');
            $business_id = $request->session()->get('user.business_id');

            $count = Role::where('name', $role_name . '#' . $business_id)
                ->where('business_id', $business_id)
                ->count();
            if ($count == 0) {
                $is_service_staff = 0;
                if ($request->input('is_service_staff') == 1) {
                    $is_service_staff = 1;
                }

                $role = Role::create([
                    'name' => $role_name . '#' . $business_id,
                    'business_id' => $business_id,
                    'is_service_staff' => $is_service_staff,
                    'is_superadmin_default' => 1
                ]);

                //Include selling price group permissions
                $spg_permissions = $request->input('spg_permissions');
                if (!empty($spg_permissions)) {
                    foreach ($spg_permissions as $spg_permission) {
                        $permissions[] = $spg_permission;
                    }
                }

                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }
                $output = [
                    'success' => 1,
                    'msg' => __("user.role_added")
                ];
            } else {
                $output = [
                    'success' => 0,
                    'role' => 1,
                    'msg' => __("user.role_already_exists")
                ];
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'role' => 1,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect('superadmin/settings')->with('status', $output);
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
        if (!auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $role = Role::where('business_id', $business_id)
            ->with(['permissions'])
            ->find($id);
        $role_permissions = [];
        foreach ($role->permissions as $role_perm) {
            $role_permissions[] = $role_perm->name;
        }

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->get();

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');

        $get_permissions['mpcs_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mpcs_module');
        $get_permissions['enable_cheque_writing'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_cheque_writing');
        $get_permissions['enable_petro_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $get_permissions['issue_customer_bill'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'issue_customer_bill');
        $get_permissions['hr_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_module');
        $get_permissions['tasks_management'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tasks_management');
        $get_permissions['member_registration'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');
        $get_permissions['upload_images'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'upload_images');
        $get_permissions['leads_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_module');
        $get_permissions['sms_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sms_module');
        $get_permissions['customer_settings'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'customer_settings');
        $get_permissions['repair_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module');
        $get_permissions['mf_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module');
        $get_permissions['cache_clear'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'cache_clear');
        $get_permissions['sms_enable'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'sms_enable');
        $get_permissions['enable_restaurant'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_restaurant');
        $get_permissions['ran_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'ran_module');

        return view('superadmin::superadmin_settings.role.edit')
            ->with(compact('role', 'role_permissions', 'selling_price_groups', 'module_permissions', 'get_permissions'));
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
        if (!auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $role_name = $request->input('name');
            $permissions = $request->input('permissions');
            $business_id = $request->session()->get('user.business_id');

            $count = Role::where('name', $role_name . '#' . $business_id)
                ->where('id', '!=', $id)
                ->where('business_id', $business_id)
                ->count();
            if ($count == 0) {
                $role = Role::findOrFail($id);

                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $is_service_staff = 0;
                    if ($request->input('is_service_staff') == 1) {
                        $is_service_staff = 1;
                    }
                    $role->is_service_staff = $is_service_staff;
                    $role->name = $role_name . '#' . $business_id;
                    $role->save();

                    //Include selling price group permissions
                    $spg_permissions = $request->input('spg_permissions');
                    if (!empty($spg_permissions)) {
                        foreach ($spg_permissions as $spg_permission) {
                            $permissions[] = $spg_permission;
                        }
                    }

                    if (!empty($permissions)) {
                        $role->syncPermissions($permissions);
                    }

                    $output = [
                        'success' => 1,
                        'role' => 1,
                        'msg' => __("user.role_updated")
                    ];
                } else {
                    $output = [
                        'success' => 0,
                        'role' => 1,
                        'msg' => __("user.role_is_default")
                    ];
                }
            } else {
                $output = [
                    'success' => 0,
                    'role' => 1,
                    'msg' => __("user.role_already_exists")
                ];
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'role' => 1,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('superadmin/settings')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('roles.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $role = Role::where('business_id', $business_id)->find($id);

                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    $role->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("user.role_deleted")
                    ];
                } else {
                    $output = [
                        'success' => 0,
                        'msg' => __("user.role_is_default")
                    ];
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
