<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\LeaveRequestController@update',
        $leave_request->id) .'?tab=leave_request', 'method' =>
        'put',
        'id' => 'edit_employee_form', 'files' => true ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'hr::lang.edit_leave_request' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.date')</label>
                        <input type="text" name="date" id="datepicker" required class="form-control" value="" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_name')</label>
                        {!! Form::select('employee_name', $employee_names , $leave_request->employee_id, ['class' =>
                        'form-control', 'id' =>
                        'employee_name', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_number')</label>
                        {!! Form::select('employee_number', $employee_numbers , $leave_request->employee_id, ['class' =>
                        'form-control', 'id' =>
                        'employee_number', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_type')</label>
                        {!! Form::select('leave_type_id', $leave_types , $leave_request->leave_type_id, ['class' =>
                        'form-control', 'id' =>
                        'leave_type', 'required', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_date_from')</label>
                        <input type="text" name="leave_date_from" id="leave_date_from" required class="form-control"
                            value="" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.leave_date_to')</label>
                        <input type="text" name="leave_date_to" id="leave_date_to" required class="form-control"
                            value="" readonly>
                    </div>
                </div>
                @if (auth()->user()->can('leave_request.approve_reject'))
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.status')</label>
                        {!! Form::select('status', ['pending' => __('hr::lang.pending'), 'approved' =>
                        __('hr::lang.approved'), 'rejected' => __('hr::lang.rejected')] , $leave_request->status,
                        ['class' =>
                        'form-control', 'id' =>
                        'status', 'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                @endif

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
    $('#datepicker').datepicker('setDate', "{{!empty($leave_request->date) ? \Carbon::parse($leave_request->date)->format('d-m-Y') : null }}");
    $('#leave_date_to').datepicker('setDate', "{{!empty($leave_request->leave_date_to) ? \Carbon::parse($leave_request->leave_date_to)->format('d-m-Y') : null }}");
    $('#leave_date_from').datepicker('setDate', "{{!empty($leave_request->leave_date_from) ? \Carbon::parse($leave_request->leave_date_from)->format('d-m-Y') : null }}");

    $('#employee_number').change(function(){
        $('#employee_name').val($(this).val());
    });
    $('#employee_name').change(function(){
        $('#employee_number').val($(this).val());
    });
</script>