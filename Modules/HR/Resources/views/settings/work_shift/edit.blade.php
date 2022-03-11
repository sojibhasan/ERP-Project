<!-- Modal -->

<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">@lang('hr::lang.edit_workshift')</h4>
        </div>
        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\WorkShiftController@update', $workshift->id), 'method' => 'put']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="shift_name">@lang('hr::lang.shift_name'):</label>
                        <input type="text" class="form-control" name="shift_name" value="{{$workshift->shift_name}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shift_form">@lang('hr::lang.shift_form'):</label>
                        <input type="text" class="form-control shif_from" name="shift_form"
                            value="{{$workshift->shift_form}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shift_to">@lang('hr::lang.shift_to'):</label>
                        <input type="text" class="form-control shif_to" name="shift_to"
                            value="{{$workshift->shift_to}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>

</div>


<script>
    $('.shif_from').datetimepicker({format: 'HH:mm a' });
    $('.shif_to').datetimepicker({format: 'HH:mm a' });
</script>