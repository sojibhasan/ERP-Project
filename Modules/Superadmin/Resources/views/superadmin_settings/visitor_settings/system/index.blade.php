<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'visitor::lang.settings')])
            {!!
            Form::open(['url' => action('\Modules\Visitor\Http\Controllers\VisitorSettingController@store'),
            'method' =>'post', 'id' => 'setting_form' ])
            !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_required_name', 1, !empty($visitor_settings) ?
                                    $visitor_settings->enable_required_name: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_required_name' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_required_address', 1,!empty($visitor_settings) ?
                                    $visitor_settings->enable_required_address: null,
                                    [ 'class' => 'input-icheck']); !!}
                                    {{ __( 'visitor::lang.enable_required_address' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_required_district',
                                    1,!empty($visitor_settings) ? $visitor_settings->enable_required_district: null,
                                    [ 'class' => 'input-icheck']); !!}
                                    {{ __( 'visitor::lang.enable_required_district' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_required_town', 1,!empty($visitor_settings) ?
                                    $visitor_settings->enable_required_town: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_required_town' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_add_district', 1,!empty($visitor_settings) ?
                                    $visitor_settings->enable_add_district: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_add_district' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_add_town', 1,!empty($visitor_settings) ?
                                    $visitor_settings->enable_add_town: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_add_town' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('show_referral_code', 1,!empty($settings['show_referral_code']) ?
                                    $settings['show_referral_code']: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.show_referral_code' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_superadmin" value="1">

                    <div class="clearfix"></div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('admin_msg_visitor_qr', __('superadmin::lang.admin_msg_visitor_qr') .":")
                            !!}
                            {!! Form::textarea('admin_msg_visitor_qr', !empty($settings['admin_msg_visitor_qr']) ?
                            $settings['admin_msg_visitor_qr'] : '', ['class' => 'form-control', 'rows' => 5, 'cols' =>
                            200]) !!}
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('visitor_site_url', __('superadmin::lang.visitor_site_url') .":") !!}
                            {!! Form::text('visitor_site_url', !empty($settings['visitor_site_url']) ?
                            $settings['visitor_site_url'] : '', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('visitor_site_name', __('superadmin::lang.visitor_site_name') .":") !!}
                            {!! Form::text('visitor_site_name', !empty($settings['visitor_site_name']) ?
                            $settings['visitor_site_name'] : '', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('visitor_code_color', __('superadmin::lang.visitor_code_color').':') !!}
                            {!! Form::text('visitor_code_color', !empty($settings['visitor_code_color']) ?
                            $settings['visitor_code_color'] : '#000000', ['class' => 'form-control']); !!}
                            <br>
                            {!! Form::text('color-picker', null, ['class' => 'form-control color-picker', 'id' =>
                            'color-picker', 'placeholder' => __( 'tasksmanagement::lang.color' )]);
                            !!}
                        </div>
                    </div>
                    <div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success" style="float: right">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

</section>

<!-- /.content -->