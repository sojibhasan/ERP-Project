<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Composer\Semver\Comparator;

use App\System;

class InstallController extends BaseController
{
    public function __construct()
    {
        $this->module_name = 'superadmin';
        $this->appVersion = config('superadmin.module_version');
    }

    /**
     * Install
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();
        
        //Check if installed or not.
        $is_installed = System::getProperty($this->module_name . '_version');
        if (empty($is_installed)) {
            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('migrate', ["--force"=> true]);
        }

        $output = ['success' => 1,
                    'msg' => 'Superadmin module installed succesfully'
                ];
        return redirect()->action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')
            ->with('status', $output);
    }

    /**
     * Initialize all install functions
     *
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    //Updating
    public function update()
    {
        //Check if superadmin_version is same as appVersion then 404
        //If appVersion > superadmin_version - run update script.
        //Else there is some problem.

        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
            
            $superadmin_version = System::getProperty($this->module_name . '_version');
            
            if (Comparator::greaterThan($this->appVersion, $superadmin_version)) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                $this->installSettings();
                
                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('migrate', ["--force"=> true]);

                System::setProperty($this->module_name . '_version', $this->appVersion);
            } else {
                abort(404);
            }

            DB::commit();
            
            $output = ['success' => 1,
                        'msg' => 'Superadmin module updated Succesfully to version ' . $this->appVersion . ' !!'
                    ];
            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')
                ->with('status', $output);
        } catch (Exception $e) {
            DB::rollBack();
            die($e->getMessage());
        }
    }
}
