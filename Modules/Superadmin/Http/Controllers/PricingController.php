<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\Package;
use App\Currency;
use App\System;
use DB;

use App\Utils\ModuleUtil;
use Modules\Superadmin\Entities\GiveAwayGift;

class PricingController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $packages = Package::listPackages(true);

        //Get all module permissions and convert them into name => label
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $permission_formatted = [];
        foreach ($permissions as $permission) {
            foreach ($permission as $details) {
                $permission_formatted[$details['name']] = $details['label'];
            }
        }
        $currencies = Currency::select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country')
            ->pluck('info', 'id');

        $datetime = new \DateTimeZone("EDT");

        $timezones = $datetime->listIdentifiers();
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $accounting_methods = [
            'fifo' => __('business.fifo'),
            'lifo' => __('business.lifo')
        ];

        $package_id = request()->package;

        $show_give_away_gift_in_register_page = json_decode(System::getProperty('show_give_away_gift_in_register_page'), true);
        $show_referrals_in_register_page = json_decode(System::getProperty('show_referrals_in_register_page'), true);
        $give_away_gifts_array = GiveAwayGift::pluck('name', 'id')->toArray();
        $give_away_gifts['all'] = 'All';

        foreach ($give_away_gifts_array as $key => $value) {
            $give_away_gifts[$key] = $value;
        }


        return view('superadmin::pricing.index')
            ->with(compact(
                'packages',
                'permission_formatted',
                'currencies',
                'timezone_list',
                'months',
                'accounting_methods',
                'accounting_methods',
                'show_give_away_gift_in_register_page',
                'show_referrals_in_register_page',
                'give_away_gifts',
                'package_id'
            ));
    }
}
