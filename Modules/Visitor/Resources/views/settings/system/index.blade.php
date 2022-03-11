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
                                    {!! Form::checkbox('enable_required_name', 1, !empty($settings) ? $settings->enable_required_name: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_required_name' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_required_address', 1,!empty($settings) ? $settings->enable_required_address: null,
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
                                    1,!empty($settings) ? $settings->enable_required_district: null,
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
                                    {!! Form::checkbox('enable_required_town', 1,!empty($settings) ? $settings->enable_required_town: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_required_town' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_add_district', 1,!empty($settings) ? $settings->enable_add_district: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_add_district' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('enable_add_town', 1,!empty($settings) ? $settings->enable_add_town: null,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'visitor::lang.enable_add_town' ) }}
                                </label>
                            </div>
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