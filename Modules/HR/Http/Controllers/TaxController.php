<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\Tax;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $taxes = Tax::where('business_id', $business_id)->get();

        return view('hr::settings.tax.index')->with(compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $input = $request->except('_token');
            foreach ($input['tax'] as $tax) {
                $data = array(
                    'name' => $tax['name'],
                    'slab_amount' => $tax['slab_amount'],
                    'type' => $tax['type'],
                    'tax_rate' => $tax['tax_rate'],
                    'slab_wise_rates' => $tax['slab_wise_rates'],
                    'previous_slab' => explode(',', $tax['previous_slab'])
                );
                $data['business_id'] = $business_id;
                Tax::updateOrCreate(['id' => $tax['id']], $data);
            }
            $output = [
                'success' => true,
                'tab' => 'tax',
                'msg' => __('hr::lang.tax_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'tax',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hr::edit');
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
    public function destroy($id)
    {
        try {
            Tax::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.tax_delete_success')
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
}
