<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.home_page')])
    <div class="row">
        <div class="col-xs-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[add_related_account_label]', __('superadmin::lang.add_related_account_label') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[add_related_account_label]', !empty($help_explanations['add_related_account_label']) ? $help_explanations['add_related_account_label'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[cogs_accounts]', __('superadmin::lang.cogs_accounts') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[cogs_accounts]', !empty($help_explanations['cogs_accounts']) ? $help_explanations['cogs_accounts'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[sale_income_accounts]', __('superadmin::lang.sale_income_accounts') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[sale_income_accounts]', !empty($help_explanations['sale_income_accounts']) ? $help_explanations['sale_income_accounts'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[add_as_sub_category]', __('superadmin::lang.add_as_sub_category') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[add_as_sub_category]', !empty($help_explanations['add_as_sub_category']) ? $help_explanations['add_as_sub_category'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->  