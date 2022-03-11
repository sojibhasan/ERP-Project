<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\AttendanceController@postApproveLateOverTime',
        $attendance->id), 'method' => 'post', 'id' => 'approve_late_over_time_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'hr::lang.approve_late_over_time' )</h4>
        </div>

        <div class="modal-body">
            {!! Form::label('name', __( 'hr::lang.over_time_approved' ) .":") !!}
            <div class="form-group">
                {!! Form::text('approved_ot_hours', !empty($attendance->approved_ot_hours) ? $attendance->approved_ot_hours : null , ['class' =>
                'form-control','placeholder' => __( 'hr::lang.hours'
                ) ]); !!}
            </div>
            <div class="form-group">
                {!! Form::text('approved_ot_minutes', !empty($attendance->approved_ot_minutes) ? $attendance->approved_ot_minutes : null , ['class'
                => 'form-control','placeholder' => __( 'hr::lang.minutes'
                ) ]); !!}
            </div>
            {!! Form::label('name', __( 'hr::lang.late_time_approved' ) .":") !!}
            <div class="form-group">
                {!! Form::text('approved_lt_hours', !empty($attendance->approved_lt_hours) ? $attendance->approved_lt_hours : null , ['class' =>
                'form-control','placeholder' => __( 'hr::lang.hours'
                ) ]); !!}
            </div>
            <div class="form-group">
                {!! Form::text('approved_lt_minutes', !empty($attendance->approved_lt_minutes) ? $attendance->approved_lt_minutes : null , ['class'
                => 'form-control','placeholder' => __( 'hr::lang.minutes'
                ) ]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('name', __( 'hr::lang.status' ) .":") !!}
                {!! Form::select('status', ['approved' => __('hr::lang.approved'), 'rejected' =>
                __('hr::lang.rejected')] ,!empty($attendance->status) ? $attendance->status : null , ['class'
                => 'form-control', 'required','placeholder' => __( 'lang_v1.please_select'
                ) ]); !!}
            </div>


        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>