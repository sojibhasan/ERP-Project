<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Charts\CommonChart;
use App\Currency;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil =  $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $fy = $this->businessUtil->getCurrentFinancialYear(1);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', 1)->first();

        // //Chart for purhcase last 30 days
        $purhcase_last_30_days = [];
        $labels = [];
        $all_purchase_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($purhcase_last_30_days[$date])) {
                $all_purchase_values[] = $purhcase_last_30_days[$date];
            } else {
                $all_purchase_values[] = 0;
            }
        }

        $pruchase_chart_1 = new CommonChart;
        $pruchase_chart_1->labels($labels)
            ->options($this->__chartOptions(__(
                'ustomer.total_purhcase',
                ['currency' => $currency->code]
            )));


        $pruchase_chart_1->dataset('customer.total_purhcase', 'line', $all_purchase_values);

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        return view('agent.home', compact('date_filters', 'pruchase_chart_1', 'widgets'));
    }

    /**
     * Show the list of resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //  
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title
                ]
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }



    /**
     * Shows profile of logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $agent_id = Auth::user()->id;
        $agent = Agent::where('id', $agent_id)->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        return view('agent.profile', compact('agent', 'languages'));
    }

    /**
     * updates user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $agent_id = Auth::user()->id;
            $input = $request->only([
                'email',
                'mobile_number',
                'land_number',
                'contact_number',
                'address',
            ]);

            if (!empty($request->hasFile('agent_photo'))) {
                $input['agent_photo'] = $this->businessUtil->uploadFile($request, 'agent_photo', 'agents', 'image');
            }
            $agent = Agent::find($agent_id);
            $agent->update($input);

            //update session
            $input['id'] = $agent_id;
            session()->put('user', $input);

            $output = [
                'success' => 1,
                'msg' => 'Profile updated successfully'
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Something went wrong, please try again'
            ];
        }
        return redirect('agent/profile')->with('status', $output);
    }

    /**
     * updates user password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $agent_id = Auth::user()->id;
            $agent = Agent::where('id', $agent_id)->first();

            if (Hash::check($request->input('current_password'), $agent->password)) {
                $agent->password = Hash::make($request->input('new_password'));
                $agent->save();
                $output = [
                    'success' => 1,
                    'msg' => 'Password updated successfully'
                ];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => 'You have entered wrong password'
                ];
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Something went wrong, please try again'
            ];
        }
        return redirect('agent/profile')->with('status', $output);
    }
}
