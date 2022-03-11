<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\CrewController@store'), 'method' =>
    'post', 'id' => 'crew_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.crew' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('joined_date', __( 'ezyboat::lang.joined_date' ) . ':*') !!}
          {!! Form::text('joined_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'ezyboat::lang.joined_date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('employee_no', __( 'ezyboat::lang.employee_no' ) . ':*') !!}
          {!! Form::text('employee_no', $employee_no, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.employee_no'), 'id'
          => 'employee_no', 'readonly']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('crew_name', __( 'ezyboat::lang.crew_name' ) . ':*') !!}
          {!! Form::text('crew_name', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.crew_name'), 'id'
          => 'crew_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('nic_number', __( 'ezyboat::lang.nic_number' ) . ':*') !!}
          {!! Form::text('nic_number', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.nic_number'), 'id'
          => 'nic_number']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('license_number', __( 'ezyboat::lang.license_number' ) . ':*') !!}
          {!! Form::text('license_number', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.license_number'), 'id'
          => 'license_number']); !!}
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
 $('#joined_date').datepicker('setDate', new Date());
</script>