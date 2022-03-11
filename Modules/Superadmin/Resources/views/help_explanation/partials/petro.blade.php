<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.home_page')])
    <div class="row">
        <div class="col-xs-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[bulk_tank]', __('superadmin::lang.bulk_tank') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[bulk_tank]', !empty($help_explanations['bulk_tank']) ? $help_explanations['bulk_tank'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[bulk_sale_meter]', __('superadmin::lang.bulk_sale_meter') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[bulk_sale_meter]', !empty($help_explanations['bulk_sale_meter']) ? $help_explanations['bulk_sale_meter'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->  