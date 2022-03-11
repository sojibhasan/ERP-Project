<div class="modal-dialog" role="document" style="width: 40%">
    <div class="modal-content print">
        <div class="modal-header">
            <h5 class="modal-title">@lang('patient.enter_amount')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="col-md-12">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('variable_code', __('superadmin::lang.variable_code') . ':') !!}
                    {!! Form::text('edit_variable_code', $package_variable->variable_code, ['class' => 'form-control', 'placeholder' => 'Please select', 'id' => 'edit_variable_code']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('variable_options', __('superadmin::lang.variable_options') . ':') !!}
                    {!! Form::select('edit_variable_options', ['Number of Branches','Number of Users', 'Number of
                    Products',
                    'Number of Periods', 'Number of Customers', 'Monthly Total Sales', 'No of Family Members', 'No of Vehicles'], $package_variable->variable_options, ['class' => 'form-control', 'placeholder'
                    => 'Please select', 'id' => 'edit_variable_options']); !!}
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('option_value', __('superadmin::lang.option_value') . ':') !!}
                    <div class="input-group">
                        {!! Form::text('edit_option_value', $package_variable->option_value , ['class' =>
                        'form-control', 'id' => 'edit_option_value', 'placeholder' => __('superadmin::lang.option_value')]);
                        !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('increase_decrease', __('superadmin::lang.increase_decrease') . ':') !!}
                    {!! Form::select('edit_increase_decrease', ['Increase','Decrease'],
                    $package_variable->increase_decrease, ['id' => 'edit_increase_decrease', 'class' => 'form-control',
                    'placeholder' => 'Please select']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('variable_type', __('superadmin::lang.variable_type') . ':') !!}
                    {!! Form::select('edit_variable_type', ['Fixed','Percentage'], $package_variable->variable_type,
                    ['id' => 'edit_variable_type', 'class' => 'form-control', 'placeholder' => 'Please select']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('price_value', __('superadmin::lang.price_value') . ':') !!}
                    <div class="input-group">
                        {!! Form::text('edit_price_value', $package_variable->price_value, ['class' =>
                        'form-control','placeholder' => __('superadmin::lang.price_value'), 'id' => 'edit_price_value']); !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="row" style="margin: 20px; padding-bottom:20px;">
            <button class="btn btn-primary pull-right" id="package_variable_edit"
                type="submit">@lang('messages.submit')</button>
        </div>
    </div>
</div>

<script>
    $('#package_variable_edit').click(function(e){
        e.preventDefault();
        $.ajax({
            method: 'put',
            url: "{{action('\Modules\Superadmin\Http\Controllers\PackageVariableController@update', $package_variable->id)}}",
            data: { 
                'variable_options': $('#edit_variable_options').val(),
                'variable_code': $('#edit_variable_code').val(),
                'option_value': $('#edit_option_value').val(),
                'increase_decrease': $('#edit_increase_decrease').val(),
                'variable_type': $('#edit_variable_type').val(),
                'price_value': $('#edit_price_value').val(),
             },
            success: function(result) {
                console.log(result);
                if(result.success == 1){
                    toastr.success(result.msg);
                    package_variables_table.ajax.reload();
                }else{
                    toastr.error(result.msg);
                }
                $('.edit_modal').modal('hide');
            },
        });
    });
</script>