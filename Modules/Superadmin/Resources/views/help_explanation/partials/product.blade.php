<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.home_page')])
    <div class="row">
        <div class="col-xs-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[sku]', __('superadmin::lang.sku') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[sku]', !empty($help_explanations['sku']) ? $help_explanations['sku'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[related_sub_units]', __('superadmin::lang.related_sub_units') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[related_sub_units]', !empty($help_explanations['related_sub_units']) ? $help_explanations['related_sub_units'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[business_location]', __('superadmin::lang.business_location') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[business_location]', !empty($help_explanations['business_location']) ? $help_explanations['business_location'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[alert_quantity]', __('superadmin::lang.alert_quantity') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[alert_quantity]', !empty($help_explanations['alert_quantity']) ? $help_explanations['alert_quantity'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[manage_stock]', __('superadmin::lang.manage_stock') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[manage_stock]', !empty($help_explanations['manage_stock']) ? $help_explanations['manage_stock'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[stock_account_names]', __('superadmin::lang.stock_account_names') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[stock_account_names]', !empty($help_explanations['stock_account_names']) ? $help_explanations['stock_account_names'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[enable_product_description_imei]', __('superadmin::lang.enable_product_description_imei') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[enable_product_description_imei]', !empty($help_explanations['enable_product_description_imei']) ? $help_explanations['enable_product_description_imei'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[not_for_selling]', __('superadmin::lang.not_for_selling') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[not_for_selling]', !empty($help_explanations['not_for_selling']) ? $help_explanations['not_for_selling'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[rack_row_position_details]', __('superadmin::lang.rack_row_position_details') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[rack_row_position_details]', !empty($help_explanations['rack_row_position_details']) ? $help_explanations['rack_row_position_details'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[product_type]', __('superadmin::lang.product_type') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[product_type]', !empty($help_explanations['product_type']) ? $help_explanations['product_type'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[margin_percentage]', __('superadmin::lang.margin_percentage') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[margin_percentage]', !empty($help_explanations['margin_percentage']) ? $help_explanations['margin_percentage'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->  