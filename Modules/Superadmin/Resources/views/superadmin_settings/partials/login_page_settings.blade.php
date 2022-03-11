<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_member_register_btn_login_page', 1,
                            !empty($settings["enable_member_register_btn_login_page"]) ?
                            (int)$settings["enable_member_register_btn_login_page"] : 0 ,
                            [ 'class' => 'input-icheck']); !!}
                            {{ __( 'superadmin::lang.enable_member_register_btn_login_page' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_patient_register_btn_login_page', 1,
                            !empty($settings["enable_patient_register_btn_login_page"]) ?
                            (int)$settings["enable_patient_register_btn_login_page"] : 0 ,
                            [ 'class' => 'input-icheck']); !!}
                            {{ __( 'superadmin::lang.enable_patient_register_btn_login_page' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_visitor_register_btn_login_page', 1,
                            !empty($settings["enable_visitor_register_btn_login_page"]) ?
                            (int)$settings["enable_visitor_register_btn_login_page"] : 0 ,
                            [ 'class' => 'input-icheck']); !!}
                            {{ __( 'superadmin::lang.enable_visitor_register_btn_login_page' ) }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_register_btn_login_page', 1,
                            !empty($settings["enable_register_btn_login_page"]) ?
                            (int)$settings["enable_register_btn_login_page"] : 0 ,
                            [ 'class' => 'input-icheck']); !!}
                            {{ __( 'superadmin::lang.enable_register_btn_login_page' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_agent_register_btn_login_page', 1,
                            !empty($settings["enable_agent_register_btn_login_page"]) ?
                            (int)$settings["enable_agent_register_btn_login_page"] : 0 ,
                            [ 'class' => 'input-icheck']); !!}
                            {{ __( 'superadmin::lang.enable_agent_register_btn_login_page' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_customer_login', 1, !empty($settings["enable_customer_login"]) ?
                            (int)$settings["enable_customer_login"] : 0 ,
                            [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_customer_login' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_agent_login', 1, !empty($settings["enable_agent_login"]) ?
                            (int)$settings["enable_agent_login"] : 0 ,
                            [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_agent_login' ) }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_employee_login', 1, !empty($settings["enable_employee_login"]) ?
                            (int)$settings["enable_employee_login"] : 0 ,
                            [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_employee_login' ) }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>