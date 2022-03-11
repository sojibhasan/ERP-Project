<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.home_page')])
    <div class="row">
        <div class="col-xs-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[calculation_percentage]', __('superadmin::lang.calculation_percentage') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[calculation_percentage]', !empty($help_explanations['calculation_percentage']) ? $help_explanations['calculation_percentage'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[customer_reference]', __('superadmin::lang.customer_reference') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[customer_reference]', !empty($help_explanations['customer_reference']) ? $help_explanations['customer_reference'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[enable_separate_customer_statement_no]', __('superadmin::lang.enable_separate_customer_statement_no') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[enable_separate_customer_statement_no]', !empty($help_explanations['enable_separate_customer_statement_no']) ? $help_explanations['enable_separate_customer_statement_no'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->  