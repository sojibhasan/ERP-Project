<?php

namespace Modules\Superadmin\Http\Controllers;

use App\AccountGroup;
use App\Business;
use App\Exports\AccountExport;
use App\Exports\AccountGroupExport;
use App\Exports\AccountTypeExport;
use App\Imports\AccountGroupImport;
use App\Imports\AccountImport;
use App\Imports\AccountTypeImport;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $busineses = Business::pluck('name', 'id');
        $types = $this->businessUtil->getImportExportType();

        return view('superadmin::import_export.index')->with(compact(
            'busineses',
            'types'
        ));
    }

    /**
     * get the exported file 
     * @param int $id
     * @return Renderable
     */
    public function exportFile(Request $request)
    {
        $busines_id = $request->business_id;
        $type = $request->type;

        try {
            if ($type == 'account_list') {
                return Excel::download(new AccountExport($busines_id), 'account_list_' . $busines_id . '.xlsx');
            }
            if ($type == 'account_types') {
                return Excel::download(new AccountTypeExport($busines_id), 'account_types_' . $busines_id . '.xlsx');
            }
            if ($type == 'account_groups') {
                return Excel::download(new AccountGroupExport($busines_id), 'account_groups_' . $busines_id . '.xlsx');
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
    }

    /**
     * store the imported file 
     * @param int $id
     * @return Renderable
     */
    public function importFile(Request $request)
    {
        $busines_id = $request->business_id;
        $type = $request->type;

        try {
            if ($type == 'account_list') {
                Excel::import(new AccountImport($this->transactionUtil, $busines_id), request()->file('file'));
            }
            if ($type == 'account_types') {
                Excel::import(new AccountTypeImport($busines_id), request()->file('file'));
            }
            if ($type == 'account_groups') {
                Excel::import(new AccountGroupImport($busines_id), request()->file('file'));
            }

            $output = [
                'success' => 1,
                'msg' => __('superadmin::lang.success')
            ];

            return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
    }
}
