<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\LeaveRequestController@store') .'?tab=leave_request', 'method' =>
        'post',
        'id' => 'add_employee_form', 'files' => true ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'hr::lang.add_leave_request' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.date')</label>
                        <input type="text" name="date" id="datepicker" required class="form-control" value=""
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_name')</label>
                        {!! Form::select('employee_name', $employee_names , null, ['class' => 'form-control', 'id' =>
                        'employee_name', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                {!! Form::hidden('employee_id', 0, ['id' => 'employee_id', 'required']) !!}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_number')</label>
                        {!! Form::select('employee_number', $employee_numbers , null, ['class' => 'form-control', 'id' =>
                        'employee_number', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_type')</label>
                        {!! Form::select('leave_type_id', $leave_types , null, ['class' => 'form-control', 'id' =>
                        'leave_type', 'required', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_date_from')</label>
                        <input type="text" name="leave_date_from" id="leave_date_from" required class="form-control" value=""
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_date_to')</label>
                        <input type="text" name="leave_date_to" id="leave_date_to" required class="form-control" value=""
                            readonly>
                    </div>
                </div>
              
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#datepicker').datepicker('setDate', new Date());
    $('#leave_date_to').datepicker('setDate', new Date());
    $('#leave_date_from').datepicker('setDate', new Date());

    $('#employee_number').change(function(){
        $('#employee_name').val($(this).val());
        $('#employee_id').val($(this).val());
    });
    $('#employee_name').change(function(){
        $('#employee_number').val($(this).val());
        $('#employee_id').val($(this).val());
    });
</script>