<?php

namespace Modules\Ran\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ran\Entities\GoldGrade;
use Modules\Ran\Entities\GoldPrice;

class GoldController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');

        $gold_grades = GoldGrade::where('business_id', $business_id)->get();
        $gold_prices = GoldPrice::where('gold_prices.business_id', $business_id)->leftjoin('users', 'gold_prices.created_by', 'users.id')->select('gold_prices.*', 'users.username')->orderBy('gold_prices.id', 'desc')->get();

        return view('ran::gold.index')->with(compact(
            'gold_grades',
            'gold_prices'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('ran::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('ran::edit');
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
}
