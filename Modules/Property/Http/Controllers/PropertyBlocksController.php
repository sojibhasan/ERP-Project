<?php

namespace Modules\Property\Http\Controllers;

use App\Contact;
use App\Unit;
use App\User;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyBlock;
use Excel;

class PropertyBlocksController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    /**
     * Constructor
     *
     * @param Util $Util
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }

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
        $business_id = request()->session()->get('user.business_id');

        $property_id = request()->id;
        $property = Property::findorFail($property_id);
        $units = Unit::getPropertyUnitDropdown($business_id, false, false, 'show_in_sell_land_block_unit');

        $unitquery = Unit::where('business_id', $business_id)->where('is_property', 1)->first();    
        if(!empty($unitquery))
        {
            $unitid = $unitquery->id;
        }
        else
        {
            $unitid = '';
        }
        
        return view('property::property_blocks.create')->with(compact(
            'property',
            'units',
            'unitid'
        ));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $input['business_id'] = $business_id;
            $input['property_id'] = $request->property_id;
            $input['added_by'] = Auth::user()->id;
            $input['transaction_date'] = !empty($request->transaction_date) ? $this->commonUtil->uf_date($request->transaction_date) : date('Y-m-d');

            foreach ($request->blocks as $block) {
                $input['block_number'] = $block['block_number'];
                $input['block_extent'] = $block['block_extent'];
                $input['unit_id'] = $block['unit_id'];
                $input['block_sale_price'] = $block['block_sale_price'];

                PropertyBlock::create($input);
            }
            $output = [
                'success' => true,
                'msg' => __('property::lang.blocks_added_success')
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
        $business_id = request()->session()->get('user.business_id');

        $property = Property::findorFail($id);
        $property_blocks = PropertyBlock::leftjoin('units', 'property_blocks.unit_id', 'units.id')
            ->leftjoin('contacts', 'property_blocks.customer_id', 'contacts.id')
            ->where('property_id', $id)
            ->select('units.actual_name', 'property_blocks.*', 'contacts.name as customer_name')
            ->get();
        $units = Unit::getPropertyUnitDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $block_numbers = PropertyBlock::where('property_id', $id)->pluck('block_number', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');


        return view('property::property_blocks.show')->with(compact(
            'property',
            'property_blocks',
            'units',
            'customers',
            'block_numbers',
            'users'
        ));
    }

    /**
     * Show the resource in table tr
     * @param int $id
     * @return Renderable
     */
    public function getBlockTr($id)
    {
       
        $business_id = request()->session()->get('user.business_id');
        $query = PropertyBlock::leftjoin('units', 'property_blocks.unit_id', 'units.id')
            ->leftjoin('contacts', 'property_blocks.customer_id', 'contacts.id')
            ->where('property_id', $id)
            ->select('units.actual_name', 'property_blocks.*', 'contacts.name as customer_name', 'contacts.nic_number as customer_nic_number');

        if (!empty(request()->user_id)) {
            $users = User::where('business_id', $business_id)->where('username','like','%'.request()->user_id.'%')->pluck('username', 'id');
            $users_id = array_keys($users->toarray());
            $query->whereIn('property_blocks.sold_by', $users_id);
            
            //$query->where('property_blocks.sold_by', request()->user_id);  // old condition
        }
        if (!empty(request()->customer_id)) {
            $customers = Contact::customersSearchRecord($business_id, false, request()->customer_id);
            $customers_id = array_keys($customers->toarray());
            $query->whereIn('property_blocks.customer_id', $customers_id);
            
            // $query->where('property_blocks.customer_id', request()->customer_id); // old condition
        }
        if (!empty(request()->block_id)) {
            $block_numbers = PropertyBlock::where('property_id', $id)->pluck('block_number', 'id');
            $ress_block_id = array_search(request()->block_id,$block_numbers->toarray(),true);
            $query->where('property_blocks.id', $ress_block_id);
            
            // $query->where('property_blocks.id', request()->block_id);     // old condition
        }
        if (!empty(request()->nic_number)) {
            $query->where(
                function($q) {
                    $q->orwhere('contacts.nic_number', 'like', '%'.request()->nic_number.'%')
                          ->orwhere('contacts.name', 'like', '%'.request()->nic_number.'%');
                }
            );
            // $query->where('contacts.nic_number', 'like', '%'.request()->nic_number.'%');
            // $query->where('contacts.name', 'like', '%'.request()->nic_number.'%');
        }

        $property_blocks = $query->get();

        return view('property::property_blocks.partials.block_list_tr')->with(compact(
            'property_blocks'
        ));
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $property_id
     * @return Renderable
     */
    public function edit($property_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $property_blocks = PropertyBlock::where('property_id', $property_id)
            ->select('property_blocks.*')
            ->get();

        $property = Property::findorFail($property_id);
        $units = Unit::getPropertyUnitDropdown($business_id);


        return view('property::property_blocks.edit')->with(compact(
            'property_blocks',
            'property',
            'units'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function updateBlocks(Request $request)
    {
        try {
            $blocks = $request->blocks;

            if (!empty($blocks)) {
                DB::beginTransaction();
                foreach ($blocks as $block) {
                    $data = [];

                    $data = [
                        'block_number' => $block['block_number'],
                        'block_extent' => $block['block_extent'],
                        'unit_id' => $block['unit_id'],
                        'block_sale_price' => $this->transactionUtil->num_uf($block['block_sale_price']),
                    ];

                    PropertyBlock::where('id', $block['block_id'])->update($data);
                }
                DB::commit();
            }

            $output = [
                'success' => true,
                'msg' => __('property::lang.block_update_success')
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
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
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
    /**
     * Impport the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function getImport($id)
    {

        return view('property::property_blocks.import')->with(compact(
            'id'
        ));
    }
    /**
     * Impport the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function postImport($id, Request $request)
    {
        try {

            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('blocks_csv')) {
                $file = $request->file('blocks_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    //Check if 6 no. of columns exists
                    if (count($value) != 6) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }

                    $row_no = $key + 1;
                    $block_array = [];


                    if (!empty($value[0])) {
                        $block_array['block_number'] = $value[0];
                    } else {
                        $is_valid =  false;
                        $error_msg = "block number is required in row no. $row_no";
                        break;
                    }


                    if (!empty($value[1])) {
                        $block_array['block_sale_price'] = $value[1];
                    } else {
                        $is_valid =  false;
                        $error_msg = "block sale price is required in row no. $row_no";
                        break;
                    }

                    if (!empty($value[2])) {
                        $block_array['block_extent'] = $value[2];
                    } else {
                        $is_valid =  false;
                        $error_msg = "block extent is required in row no. $row_no";
                        break;
                    }

                    //Add unit
                    $unit_name = trim($value[3]);
                    if (!empty($unit_name)) {
                        $unit = Unit::where('business_id', $business_id)
                            ->where('is_property', 1)
                            ->where(function ($query) use ($unit_name) {
                                $query->where('short_name', $unit_name)
                                    ->orWhere('actual_name', $unit_name);
                            })->first();
                        if (!empty($unit)) {
                            $block_array['unit_id'] = $unit->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "UNIT not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = "UNIT is required in row no. $row_no";
                        break;
                    }



                    if (!empty(trim($value[5]))) {
                        $block_array['transaction_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[5]);
                    } else {
                        $block_array['transaction_date'] = date('Y-m-d');
                    }


                    $block_array['property_id'] = $id;
                    $block_array['business_id'] = $business_id;
                    $block_array['added_by'] = Auth::user()->id;

                    $formated_data[] = $block_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }
                if (!empty($formated_data)) {
                    foreach ($formated_data as $data) {

                        PropertyBlock::create($data);
                    }
                }

                DB::commit();
                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];
                return redirect()->to('/property/properties')->with('status', $output);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect()->route('accounts.import')->with('notification', $output);
        }

        return redirect()->back()->with('status', $output);
    }

    public function getBlockDetails($id)
    {
        $block = PropertyBlock::leftjoin('units', 'property_blocks.unit_id', 'units.id')->where('property_blocks.id', $id)
            ->select('property_blocks.*', DB::raw('CONCAT(actual_name, " (", short_name, ")") as unit_name'))->first();


        return ['success' => 1, 'data' => $block];
    }

    public function getBlocksDropdown($property_id)
    {
        $block = PropertyBlock::where('property_id', $property_id)->where('is_sold', 0)->where('is_finalized', 0)->select('block_number', 'id', 'is_sold')->get();

        $html = '<option>Please Select</option>';
        if (empty($block)) {
            return $html;
        }
        foreach ($block as $value) {
            $sold = '';
            if($value->is_sold){
                $sold = '<span style="color: red">- Sold</span>';
            }
            $html .= '<option value="' . $value->id . '">' . $value->block_number . ' ' .$sold . '</option>';
        }

        return ['success' => 1, 'data' => $html];
    }
}
