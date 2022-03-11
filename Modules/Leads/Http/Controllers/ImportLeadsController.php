<?php

namespace Modules\Leads\Http\Controllers;

use App\Category;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Leads\Entities\Lead;
use Modules\Leads\Entities\LeadsCategory;

class ImportLeadsController extends Controller
{
    protected $moduleUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_import')) {
            abort(403, 'Unauthorized action.');
        }
        return view('leads::import_leads.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('leads::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_import')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('leads_csv')) {
                $file = $request->file('leads_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);


                DB::beginTransaction();
                // $remove_last_row = array_pop($imported_data);
                foreach ($imported_data as $key => $value) {

                    //Check if any column is missing
                    if (count($value) < 11) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $row_no = $key + 1;
                    $leads_array = [];
                    $leads_array['business_id'] = $business_id;
                    $leads_array['created_by'] = $user_id;

                    //Add date
                    $date = trim($value[0]);
                    if (!empty($date)) {
                        $leads_array['date'] = Carbon::parse($date)->format('Y-m-d');
                        //Check if product with same date already exist
                    } else {
                        $is_valid = false;
                        $error_msg = "Date is required. $row_no";
                        break;
                    }
                    //add sector
                    $sector = trim($value[1]);
                    if (!empty($sector)) {
                        $leads_array['sector'] = strtolower($sector);
                        //Check if product with same sector already exist
                    } else {
                        $is_valid = false;
                        $error_msg = "sector is required. $row_no";
                        break;
                    }

                    //Add Category
                    //Check if category exists else create new
                    $category_name = trim($value[2]);
                    if (!empty($category_name)) {
                        $category = LeadsCategory::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $category_name],
                            ['created_by' => $user_id]
                        );
                        $leads_array['category_id'] = $category->id;
                    }

                    //Add main_organization
                    $main_organization = trim($value[3]);
                    if (!empty($main_organization)) {
                        $leads_array['main_organization'] = $main_organization;
                    }
                    //Add business
                    $business = trim($value[4]);
                    if (!empty($business)) {
                        $leads_array['business'] = $business;
                    }

                    //Add address
                    $address = trim($value[5]);
                    if (!empty($address)) {
                        $leads_array['address'] = $address;
                    }

                    //add town
                    $town = trim($value[6]);
                    if (!empty($town)) {
                        $leads_array['town'] = strtolower($town);
                        //Check if product with same town already exist
                    } else {
                        $is_valid = false;
                        $error_msg = "town is required. $row_no";
                        break;
                    }

                    //add district
                    $district = trim($value[7]);
                    if (!empty($district)) {
                        $leads_array['district'] = strtolower($district);
                        //Check if product with same district already exist
                    } else {
                        $is_valid = false;
                        $error_msg = "district is required. $row_no";
                        break;
                    }


                    //add mobile_no_1
                    $mobile_no_1 = trim($value[8]);
                    if (!empty($mobile_no_1)) {
                        $leads_array['mobile_no_1'] = strtolower($mobile_no_1);
                        //Check if product with same mobile_no_1 already exist
                    } else {
                        $is_valid = false;
                        $error_msg = "mobile no 1 is required. $row_no";
                        break;
                    }

                    //Add mobile_no_2
                    $mobile_no_2 = trim($value[9]);
                    if (!empty($mobile_no_2)) {
                        $leads_array['mobile_no_2'] = $mobile_no_2;
                    }


                    //Add mobile_no_3
                    $mobile_no_3 = trim($value[10]);
                    if (!empty($mobile_no_3)) {
                        $leads_array['mobile_no_3'] = $mobile_no_3;
                    }


                    //Add land_number
                    $land_number = trim($value[11]);
                    if (!empty($land_number)) {
                        $leads_array['land_number'] = $land_number;
                    }

                    Lead::create($leads_array);
                }
            }

            DB::commit();


            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect('leads/import')->with('notification', $output);
        }

        return redirect('leads/leads')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('leads::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('leads::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
