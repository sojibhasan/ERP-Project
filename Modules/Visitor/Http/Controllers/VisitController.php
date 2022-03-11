<?php

namespace Modules\Visitor\Http\Controllers;

use App\Business;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Visitor\Entities\Visit;
use Yajra\DataTables\Facades\DataTables;

class VisitController extends Controller
{
      /**
     * Display a listing of the resource.
     * @return Response
     */
    public function home()
    {
        if (request()->ajax()) {
            $visits = Visit::leftjoin('business', 'visits.business_id', 'business.id')
                ->where('visits.visitor_id', auth()->user()->id)
                ->select([
                    'visits.*',
                    'business.name as business_name'
                ]);
            if (!empty(request()->business_id)) {
                $visits->where('visits.business_id', request()->business_id);
            }
    
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $visits->whereDate('visits.visited_date', '>=', request()->start_date);
                $visits->whereDate('visits.visited_date', '<=', request()->end_date);
            }
            return DataTables::of($visits)
                ->removeColumn('id')
                ->rawColumns([])
                ->make(true);
        }

        $businesses = Business::pluck('name', 'id');

        return view('visitor::visitor.home')->with(compact(
            'businesses'
        ));
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('visitor::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('visitor::create');
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
        return view('visitor::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('visitor::edit');
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
