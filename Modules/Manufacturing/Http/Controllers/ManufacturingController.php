<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\BusinessLocation;
use App\System;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Manufacturing\Entities\MfgRecipe;
use Modules\Manufacturing\Utils\ManufacturingUtil;

class ManufacturingController extends Controller
{
     /**
     * All Utils instance.
     *
     */
    protected $mfgUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ManufacturingUtil $mfgUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->mfgUtil = $mfgUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        $recipe_dropdown = MfgRecipe::forDropdown($business_id);
        $manufacturing_settings = $this->mfgUtil->getSettings($business_id);

        $version = System::getProperty('manufacturing_version');
        return view('manufacturing::index')->with(compact(
            'business_locations',
            'recipe_dropdown',
            'manufacturing_settings',
            'version'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('manufacturing::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('manufacturing::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('manufacturing::edit');
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
