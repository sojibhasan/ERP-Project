<div class="pos-tab-content">
    <div class="row">
        <h3>@lang('superadmin::lang.login_page_banner')</h3>
        <div class="col-xs-12">
            <div class="col-sm-6 equal-column">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_login_banner_image', 1,
                            isset($settings["enable_login_banner_image"]) ? (int)$settings["enable_login_banner_image"]
                            : false, ['class' => 'input-icheck']);
                            !!}
                            @lang('superadmin::lang.banner_image')
                        </label>
                    </div>
                    {!! Form::file('login_banner_image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                    <small>
                        <p class="help-block">@lang('purchase.max_file_size', ['size' =>
                            (config('constants.document_size_limit') /
                            1000000)])</p>
                    </small>
                </div>
            </div>
            <div class="col-sm-6 equal-column">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_login_banner_html', 1,
                            isset($settings["enable_login_banner_html"]) ? (int)$settings["enable_login_banner_html"] :
                            false, ['class' => 'input-icheck']);
                            !!}
                            @lang('superadmin::lang.banner_html')
                        </label>
                    </div>
                    {!! Form::textarea('login_banner_html', isset($settings["login_banner_html"]) ?
                    $settings["login_banner_html"] : null, ['class' => 'form-control','id' => 'login_banner_html',
                    'placeholder' => __('superadmin::lang.banner_html'), 'rows' => 2]); !!}
                </div>
            </div>
        </div>
    </div>
</div>