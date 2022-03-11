<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('CRMActivityController@store'), 'method' => 'post']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.add_crm_activity')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-md-4 supplier_fields">
                    <div class="form-group">
                        {!! Form::label('date', __('lang_v1.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('lang_v1.date'), 'required']); !!}
                        </div>
                    </div>
                </div>
                @php
                $last_contact_id = DB::table('contacts')->select('id','contact_id')->orderBy('id', 'desc')->first();
                if(empty($last_contact_id )){
                $last_contact_id = '0000';
                }else{

                $last_contact = $last_contact_id->contact_id;
                $numbers = preg_replace('/[^0-9]/', '', $last_contact);
                $letters = preg_replace('/[^a-zA-Z]/', '', $last_contact);
                $numbers++;
                $last_contact_id = $letters.'000'.$numbers;

                }
                @endphp

                {!! Form::hidden('contact_id', $last_contact_id, []); !!}


                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('lang_v1.name') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('name', null, ['class' => 'form-control input_number', 'required',
                            'placeholder' => __('lang_v1.name'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('email', __('business.email') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            {!! Form::email('email', null, ['class' => 'form-control','placeholder' =>
                            __('business.email'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-mobile"></i>
                            </span>
                            {!! Form::text('mobile', null, ['class' => 'form-control input_number', 'required',
                            'placeholder' => __('contact.mobile'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text('alternate_number', null, ['class' => 'form-control input_number',
                            'placeholder' => __('contact.alternate_contact_number'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('landline', __('contact.landline') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text('landline', null, ['class' => 'form-control input_number', 'placeholder' =>
                            __('contact.landline'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('city', __('business.city') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' =>
                            __('business.city'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('district', __('lang_v1.district') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('district', null, ['class' => 'form-control', 'placeholder' =>
                            __('business.state'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('country', __('business.country') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>
                            {!! Form::text('country', 'Sri Lanka', ['class' => 'form-control', 'placeholder' =>
                            __('business.country'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('time_connected', __('lang_v1.time_connected') . ':') !!}
                        {!! Form::text('time_connected', null, ['class' => 'form-control',
                        'placeholder' => __('lang_v1.time_connected'), 'required']); !!}
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
                        {!! Form::text('next_follow_up_date', null, ['class' => 'form-control', 'rows' => '4',
                        'placeholder' => __('lang_v1.next_follow_up_date') , 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('add_in_customer_page', __('lang_v1.add_in_customer_page') . ':') !!}
                        {!! Form::select('add_in_customer_page', ['0' => 'No', '1' => 'Yes'] ,null, ['class' =>
                        'form-control',
                        'placeholder' => __('lang_v1.please_select'), 'required']); !!}
                    </div>
                </div>

                <div class="col-md-4 customer_fields hide">
                    <div class="form-group">
                        {!! Form::label('password', __('business.password') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>
                            </span>

                            {!! Form::password('password', ['class' => 'form-control', 'id' => 'password','placeholder'
                            =>
                            __('business.password')]); !!}
                        </div>
                        <p class="help-block">At least 6 character.</p>
                    </div>
                </div>
                <div class="col-md-4 customer_fields hide">
                    <div class="form-group">
                        {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>,
                            </span>
                            {!! Form::password('confirm_password', ['class' => 'form-control', 'id' =>
                            'confirm_password',
                            'placeholder' => __('business.confirm_password')]); !!}
                        </div>
                        <p class="help-block">At least 6 character.</p>
                    </div>
                </div>
                <div class="clearfix"></div>

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
    $('#date').datepicker('setDate', new Date());
    $('#next_follow_up_date').datetimepicker();
    $('#time_connected').datetimepicker({
                format: 'HH:mm'
            });

    $(document).on('change', '#add_in_customer_page', function(){
        console.log($(this).val());
        if($(this).val() === '1'){
            $('.customer_fields').removeClass('hide');
            $('#password').prop('required', true);
            $('#confirm_password').prop('required', true);
        }else{
            $('.customer_fields').addClass('hide');
            $('#password').prop('required', false);
            $('#confirm_password').prop('required', false);
        }
    })
</script>