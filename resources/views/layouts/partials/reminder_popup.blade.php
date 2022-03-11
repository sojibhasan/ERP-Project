<div class="modal" tabindex="-1" role="dialog" id="reminder_modal_{{$value->id}}" style="margin-top: 10%; border-radius: 10px;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body text-center">
                <i class="fa fa-bell-o fa-lg" style="font-size: 50px; margin-top: 20px; border: 1px solid #333; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h3>{{$value->name}}</h3>

                @if(!empty($value->crm_reminder_id))
                <div class="clearfix"></div>
                <div class="col-md12">
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#open_reminder_modal_{{$value->id}}">@lang('lang_v1.open')</button>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#details_reminder_modal_{{$value->id}}">@lang('lang_v1.details')</button>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <div class="col-md-10">
                    {!! Form::open(['url' =>
                    action('\Modules\TasksManagement\Http\Controllers\ReminderController@snoozeReminder',
                    $value->id), 'method' => 'post', 'id' => 'reminder_modal_form']) !!}
                    @if($value->crm_reminder == 0)
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="time" placeholder="Time">
                    </div>
                    <div class="col-md-4">
                        <select name="time_type" id="time_type" class="form-control">
                            <option value="minutes">Minutes</option>
                            <option value="hours">Hours</option>
                            <option value="days">Days</option>
                            <option value="weeks">Weeks</option>
                            <option value="months">Months</option>
                        </select>
                    </div>
                    @else
                    <div class="col-md-4">
                        <input type="text" class="form-control snooze_time" required name="snooze_time" placeholder="Time">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control snooze_date" required name="snooze_date" placeholder="Date">
                    </div>
                    @endif
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">@lang('lang_v1.snooze')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-md-2">
                    <a href="{{action('\Modules\TasksManagement\Http\Controllers\ReminderController@cancelReminder', $value->id)}}" class="btn btn-default" >Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#reminder_modal_{{$value->id}}').modal('show');
</script>