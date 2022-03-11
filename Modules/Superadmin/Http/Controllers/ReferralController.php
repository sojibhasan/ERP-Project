<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Contact;
use App\Customer;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Referral;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $referrals = Referral::leftjoin('business', 'referrals.resource_id', 'business.id')
                ->leftjoin('packages', 'referrals.package_id', 'packages.id')
                ->select('referrals.*', 'business.name as name_of_registeration', 'business.id as business_id', 'business.company_number',  'packages.name as package_name');

            if (!empty(request()->package_id)) {
                $referrals->where('package_id', request()->package_id);
            }
            if (!empty(request()->referral_code)) {
                $referrals->where('referrals.id', request()->referral_code);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $referrals->whereDate('referrals.created_at', '>=', request()->start_date);
                $referrals->whereDate('referrals.created_at', '<=', request()->end_date);
            }

            return DataTables::of($referrals)

                ->editColumn('created_at', '{{@format_date($created_at)}}')

                ->editColumn('name_of_registeration', function ($row) {
                    if ($row->model_type == 'patient') {
                        $patient = User::where('business_id', $row->business_id)->first();
                        return $patient->first_name . ' ' . $patient->last_name;
                    }
                    if ($row->model_type == 'business') {
                        return $row->name_of_registeration;
                    }
                    if ($row->model_type == 'customer') {
                        $customer = Customer::where('id', $row->resource_id)->first();
                        return $customer->first_name . ' ' . $customer->last_name;
                    }
                })
                ->editColumn('company_number', function ($row) {
                    if ($row->model_type == 'patient') {
                        $patient = User::where('business_id', $row->business_id)->first();
                        return $patient->username;
                    }
                    if ($row->model_type == 'business') {
                        return $row->company_number;
                    }
                    if ($row->model_type == 'customer') {
                        $customer = Customer::where('id', $row->resource_id)->first();
                        return $customer->username;
                    }
                })

                ->removeColumn('id')
                ->rawColumns(['action', 'multiple_units'])
                ->make(true);
        }

        $referral_codes = Referral::pluck('referral_code', 'id');
        $packages = Package::pluck('name', 'id');

        return view('superadmin::referral.index')->with(compact(
            'referral_codes',
            'packages'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('superadmin::create');
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
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('superadmin::edit');
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
