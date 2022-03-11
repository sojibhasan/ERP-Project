<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.home_page')])
    <div class="row">
        <div class="col-xs-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[allow_decimal]', __('superadmin::lang.allow_decimal') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[allow_decimal]', !empty($help_explanations['allow_decimal']) ? $help_explanations['allow_decimal'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('help_explanation[add_as_multiple_of_other_unit]', __('superadmin::lang.add_as_multiple_of_other_unit') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('help_explanation[add_as_multiple_of_other_unit]', !empty($help_explanations['add_as_multiple_of_other_unit']) ? $help_explanations['add_as_multiple_of_other_unit'] : null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->  