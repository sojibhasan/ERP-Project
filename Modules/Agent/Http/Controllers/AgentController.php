<?php

namespace Modules\Agent\Http\Controllers;

use App\BusinessLocation;
use App\Charts\CommonChart;
use App\Currency;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\HelpExplanation;

class AgentController extends Controller
{
    /**
     * Display dashboard
     * @return Renderable
     */
    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');
            
        if (session()->get('business.is_patient')) {
            return redirect('patient');
        }
        if (session()->get('business.is_hospital') || session()->get('business.is_laboratory')) {
            return redirect('hospital');
        }
        $home_dashboard =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'home_dashboard');
        $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');
        $property_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');


        $disable_all_other_module_vr =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'disable_all_other_module_vr');
        if ($disable_all_other_module_vr && !auth()->user()->can('superadmin')) {
            return redirect()->to('visitor-module/visitor');
        }

        if (!auth()->user()->can('dashboard.data')) {
            return view('home.index')->with(compact('home_dashboard', 'property_module', 'enable_petro_module'));
        }


        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();

        //Chart for sells last 30 days
        $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($sells_last_30_days[$date])) {
                $all_sell_values[] = $sells_last_30_days[$date];
            } else {
                $all_sell_values[] = 0;
            }
        }

        //Get sell for indivisual locations
        $all_locations = BusinessLocation::forDropdown($business_id);
        $location_sells = [];
        $sells_by_location = $this->transactionUtil->getSellsLast30Days($business_id, true);
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            foreach ($dates as $date) {
                $sell = $sells_by_location->first(function ($item) use ($loc_id, $date) {
                    return $item->date == $date &&
                        $item->location_id == $loc_id;
                });

                if (!empty($sell)) {
                    $values[] = $sell->total_sells;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }

        $sells_chart_1 = new CommonChart;

        $sells_chart_1->labels($labels)
            ->options($this->__chartOptions(__(
                'home.total_sells',
                ['currency' => $currency->code]
            )));

        if (!empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }

        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations'), 'line', $all_sell_values);
        }

        //Chart for sells this financial year
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);

        $labels = [];
        $values = [];

        $months = [];
        $date = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));

        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $month_number = date('m', $date);

            $labels[] = Carbon::createFromFormat('m-Y', $month_year)
                ->format('M-Y');
            $date = strtotime('+1 month', $date);

            if (!empty($sells_this_fy[$month_year])) {
                $values[] = $sells_this_fy[$month_year];
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $fy_sells_by_location = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end'], true);
        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $sell = $fy_sells_by_location->first(function ($item) use ($loc_id, $month) {
                    return $item->yearmonth == $month &&
                        $item->location_id == $loc_id;
                });

                if (!empty($sell)) {
                    $values_data[] = $sell->total_sells;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }

        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
            ->options($this->__chartOptions(__(
                'home.total_sells',
                ['currency' => $currency->code]
            )));
        if (!empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations'), 'line', $values);
        }

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        $pending_customer_payments = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->select('name')->where('transactions.business_id', $business_id)->where('payment_status', 'pending')->groupBy('name')->pluck('name')->toArray();
        $customer_name_payment = implode(',', $pending_customer_payments);

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $register_success = session('register_success');

        return view('agent::dashboard', compact(
            'help_explanations',
            'home_dashboard',
            'date_filters',
            'sells_chart_1',
            'sells_chart_2',
            'widgets',
            'customer_name_payment',
            'all_locations',
            'pump_operator_dashboard',
            'property_module',
            'enable_petro_module',
            'register_success'
        ));
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('agent::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('agent::create');
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
        return view('agent::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('agent::edit');
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
