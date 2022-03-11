<div class="modal" tabindex="-1" role="dialog" id="details_reminder_modal_{{$value->id}}" style="margin-top: 10%;">
    <div class="modal-dialog" role="document">
        {!! Form::open(['url' =>
        action('CrmActivityDetailController@store',
        $value->id), 'method' => 'post', 'id' => 'reminder_details_modal_form']) !!}
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fa fa-bell-o fa-lg"
                    style="font-size: 50px; margin-top: 20px; border: 1px solid #333; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h3>{{$value->name}}</h3>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="col-md-12">
                <div class="col-md-4">
                    <input type="hidden" name="crm_activity_id" value="{{$value->crm_reminder_id}}">
                    <div class="form-group">
                        {!! Form::label('date', __('lang_v1.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('date', null, ['class' => 'form-control date', 'required', 'placeholder' =>
                            __('lang_v1.date'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __('lang_v1.note') . ':') !!}
                        {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => '4',
                        'placeholder' => __('lang_v1.note'), 'required']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('next_follow_up_date', __('lang_v1.next_follow_up_date') . ':') !!}
                        {!! Form::text('next_follow_up_date', null, ['class' => 'form-control next_follow_up_date',
                        'rows' => '4',
                        'placeholder' => __('lang_v1.next_follow_up_date') , 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('discontinue_follow_up', __('lang_v1.discontinue_follow_up') . ':') !!}
                        {!! Form::select('discontinue_follow_up', ['0' => 'No', '1' => 'Yes'] , null, ['class' =>
                        'form-control',
                        'placeholder' => __('lang_v1.please_select'), 'required']); !!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('lang_v1.close')</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $('.date').datepicker('setDate', new Date());
    $('.next_follow_up_date').datetimepicker();
</script>