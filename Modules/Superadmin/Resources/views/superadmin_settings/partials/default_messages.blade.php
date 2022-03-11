<div class="pos-tab-content">
    <div class="row">
        <h3 class="text-red "> @lang('lang_v1.accounting_module_messages')</h3>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('not_enalbed_module_user_font_size',
                __('superadmin::lang.not_enalbed_module_user_font_size') . ':') !!}
                {!! Form::text('not_enalbed_module_user_font_size', $settings['not_enalbed_module_user_font_size'],
                ['class' =>
                'form-control','placeholder' => __('superadmin::lang.not_enalbed_module_user_font_size'), 'style' =>
                'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('not_enalbed_module_user_color',
                __('superadmin::lang.not_enalbed_module_user_color') . ':') !!}
                {!! Form::text('not_enalbed_module_user_color', $settings['not_enalbed_module_user_color'], ['class'
                =>
                'form-control','placeholder' => __('superadmin::lang.not_enalbed_module_user_color'), 'style' =>
                'width: 100%;']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('not_enalbed_module_user_message',
                __('superadmin::lang.not_enalbed_module_user_message') . ':') !!}
                {!! Form::textarea('not_enalbed_module_user_message', $settings['not_enalbed_module_user_message'],
                ['class' =>
                'form-control','placeholder' => __('superadmin::lang.not_enalbed_module_user_message'),
                'rows' => 6, 'style' => 'width: 90%;']); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <h3 class="text-red "> @lang('lang_v1.general_message')</h3>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('customer_secrity_deposit_current_liability_checkbox', 1,
                        !empty($settings["customer_secrity_deposit_current_liability_checkbox"]) ?
                        (int)$settings["customer_secrity_deposit_current_liability_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'lang_v1.customer_secrity_deposit_current_liability_checkbox' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('supplier_secrity_deposit_current_liability_checkbox', 1,
                        !empty($settings["supplier_secrity_deposit_current_liability_checkbox"]) ?
                        (int)$settings["supplier_secrity_deposit_current_liability_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'lang_v1.supplier_secrity_deposit_current_liability_checkbox' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_pump_operator_dashbaord_checkbox', 1,
                        !empty($settings["general_message_pump_operator_dashbaord_checkbox"]) ?
                        (int)$settings["general_message_pump_operator_dashbaord_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'lang_v1.general_message_pump_operator_dashbaord_checkbox' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_petro_dashboard_checkbox', 1,
                        !empty($settings["general_message_petro_dashboard_checkbox"]) ?
                        (int)$settings["general_message_petro_dashboard_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'superadmin::lang.petro_dashboard' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_tank_management_checkbox', 1,
                        !empty($settings["general_message_tank_management_checkbox"]) ?
                        (int)$settings["general_message_tank_management_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.tank_management' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_pump_management_checkbox', 1,
                        !empty($settings["general_message_pump_management_checkbox"]) ?
                        (int)$settings["general_message_pump_management_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.pump_management' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_pumper_management_checkbox', 1,
                        !empty($settings["general_message_pumper_management_checkbox"]) ?
                        (int)$settings["general_message_pumper_management_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.pumper_management' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_daily_collection_checkbox', 1,
                        !empty($settings["general_message_daily_collection_checkbox"]) ?
                        (int)$settings["general_message_daily_collection_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.daily_collection' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_settlement_checkbox', 1,
                        !empty($settings["general_message_settlement_checkbox"]) ?
                        (int)$settings["general_message_settlement_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.settlement' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_list_settlement_checkbox', 1,
                        !empty($settings["general_message_list_settlement_checkbox"]) ?
                        (int)$settings["general_message_list_settlement_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.list_settlement' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('general_message_dip_management_checkbox', 1,
                        !empty($settings["general_message_dip_management_checkbox"]) ?
                        (int)$settings["general_message_dip_management_checkbox"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'petro::lang.dip_management' ) }}
                    </label>
                </div>

            </div>
        </div>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('customer_supplier_security_deposit_current_liability_font_size',
                __('superadmin::lang.font_size') . ':') !!}
                {!! Form::text('customer_supplier_security_deposit_current_liability_font_size',
                $settings['customer_supplier_security_deposit_current_liability_font_size'],
                ['class' =>
                'form-control','placeholder' => __('superadmin::lang.font_size'), 'style' =>
                'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('customer_supplier_security_deposit_current_liability_color',
                __('superadmin::lang.color') . ':') !!}
                {!! Form::text('customer_supplier_security_deposit_current_liability_color',
                $settings['customer_supplier_security_deposit_current_liability_color'], ['class'
                =>
                'form-control','placeholder' => __('superadmin::lang.color'), 'style' =>
                'width: 100%;']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-6">
            <div class=" form-group">
                {!! Form::label('customer_supplier_security_deposit_current_liability_message',
                __('superadmin::lang.message') . ':') !!}
                {!! Form::textarea('customer_supplier_security_deposit_current_liability_message',
                $settings['customer_supplier_security_deposit_current_liability_message'],
                ['class' =>
                'form-control','placeholder' => __('superadmin::lang.message'),
                'rows' => 6, 'style' => 'width: 90%;']); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <h3 class="text-red "> @lang('lang_v1.regsiter_success_messages')</h3>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('patient_register_success_title', __('superadmin::lang.patient_register_success_title')
                . ':') !!}

                {!! Form::text('patient_register_success_title', $settings["patient_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.patient_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('patient_register_success_msg', __('superadmin::lang.patient_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {patient_code}, {first_name}, {last_name}, {system_url}

                {!! Form::textarea('patient_register_success_msg', $settings["patient_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.patient_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('company_register_success_title', __('superadmin::lang.company_register_success_title')
                . ':') !!}

                {!! Form::text('company_register_success_title', $settings["company_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.company_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('company_register_success_msg', __('superadmin::lang.company_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {business_name}, {first_name}, {last_name}, {username}, {system_url}

                {!! Form::textarea('company_register_success_msg', $settings["company_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.company_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('customer_register_success_title', __('superadmin::lang.customer_register_success_title')
                . ':') !!}

                {!! Form::text('customer_register_success_title', $settings["customer_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.customer_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('customer_register_success_msg', __('superadmin::lang.customer_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {first_name}, {last_name}, {username}, {system_url}

                {!! Form::textarea('customer_register_success_msg', $settings["customer_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.customer_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('visitor_register_success_title', __('superadmin::lang.visitor_register_success_title')
                . ':') !!}

                {!! Form::text('visitor_register_success_title', $settings["visitor_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.visitor_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('visitor_register_success_msg', __('superadmin::lang.visitor_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {first_name}, {last_name}, {username}, {system_url}

                {!! Form::textarea('visitor_register_success_msg', $settings["visitor_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.visitor_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('member_register_success_title', __('superadmin::lang.member_register_success_title')
                . ':') !!}

                {!! Form::text('member_register_success_title', $settings["member_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.member_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('member_register_success_msg', __('superadmin::lang.member_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {name}, {username}, {system_url}

                {!! Form::textarea('member_register_success_msg', $settings["member_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.member_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('agent_register_success_title', __('superadmin::lang.agent_register_success_title')
                . ':') !!}

                {!! Form::text('agent_register_success_title', $settings["agent_register_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.agent_register_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('agent_register_success_msg', __('superadmin::lang.agent_register_success_msg')
                . ':') !!} <br><strong>@lang('lang_v1.available_tags'):</strong> {name}, {username}, {system_url}, {referral_code}

                {!! Form::textarea('agent_register_success_msg', $settings["agent_register_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.agent_register_success_msg'), 'rows'
                => 3]);
                !!}
            </div>
        </div>

        <div class="clearfix"></div>
        <h3 class="text-red "> @lang('superadmin::lang.subscription_messages')</h3>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('subscription_message_online_success_title', __('superadmin::lang.subscription_message_success_title')
                . ':') !!} ({{__('superadmin::lang.online')}})

                {!! Form::text('subscription_message_online_success_title', $settings["subscription_message_online_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.subscription_message_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('subscription_message_online_success_msg', __('superadmin::lang.subscription_message_success_message_body')
                . ':') !!} ({{__('superadmin::lang.online')}}) <br><strong>@lang('lang_v1.available_tags'):</strong> {package_name}, {first_name}, {last_name}, {system_url}

                {!! Form::textarea('subscription_message_online_success_msg', $settings["subscription_message_online_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.subscription_message_success_message_body'), 'rows'
                => 3]);
                !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('subscription_message_offline_success_title', __('superadmin::lang.subscription_message_success_title')
                . ':') !!} ({{__('superadmin::lang.offline')}})

                {!! Form::text('subscription_message_offline_success_title', $settings["subscription_message_offline_success_title"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.subscription_message_success_title')]);
                !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                {!! Form::label('subscription_message_offline_success_msg', __('superadmin::lang.subscription_message_success_message_body')
                . ':') !!}  ({{__('superadmin::lang.offline')}}) <br><strong>@lang('lang_v1.available_tags'):</strong> {package_name}, {first_name}, {last_name}, {system_url}

                {!! Form::textarea('subscription_message_offline_success_msg', $settings["subscription_message_offline_success_msg"],
                ['class' => 'form-control','placeholder' => __('superadmin::lang.subscription_message_success_message_body'), 'rows'
                => 3]);
                !!}
            </div>
        </div>


    </div>
</div>