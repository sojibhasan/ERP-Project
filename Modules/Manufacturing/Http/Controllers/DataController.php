<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;

class DataController extends Controller
{
    /**
     * Defines module as a superadmin package.
     * @return Array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'manufacturing_module',
                'label' => __('manufacturing::lang.manufacturing_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'manufacturing.access_recipe',
                'label' => __('manufacturing::lang.access_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.add_recipe',
                'label' => __('manufacturing::lang.add_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.edit_recipe',
                'label' => __('manufacturing::lang.edit_recipe'),
                'default' => false
            ],
            [
                'value' => 'manufacturing.access_production',
                'label' => __('manufacturing::lang.access_production'),
                'default' => false
            ]
        ];
    }
}
