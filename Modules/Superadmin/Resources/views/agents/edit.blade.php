<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\AgentController@update', $agent->id), 'method' => 'put', 'id' => 'edit_agent'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit_agent' )</h4>
        </div>

        <div class="modal-body">
            <div class="form-group col-md-6">
                {!! Form::label('name', __('business.name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('name', $agent->name, ['class' => 'form-control','placeholder' => __('business.name')]); !!}
                </div>
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('email', __('business.email') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::email('email',  $agent->email, ['class' => 'form-control','placeholder' => __('business.email') ]); !!}
                </div>
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('mobile_number', __('customer.mobile') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('mobile_number',  $agent->mobile_number, ['class' => 'form-control','placeholder' => __('customer.mobile_number') ]); !!}
                </div>
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('land_number', __('superadmin::lang.land_number') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('land_number',  $agent->land_number, ['class' => 'form-control','placeholder' => __('superadmin::lang.land_number') ]); !!}
                </div>
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('nic_number	', __('superadmin::lang.nic_number') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('nic_number',  $agent->nic_number, ['class' => 'form-control','placeholder' => __('superadmin::lang.nic_number') ]); !!}
                </div>
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('address', __('customer.address') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('address',  $agent->address, ['class' => 'form-control','placeholder' => __('customer.address') ]); !!}
                </div>
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