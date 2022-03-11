<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\InstallmentCycleController@update',
    $installment_cycle->id), 'method' =>
    'post', 'id' => 'installment_cycle_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.edit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', null, ['class' => 'form-control', 'required',
          'readonly', 'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name', __( 'property::lang.installment_cycle' ) . ':*') !!}
          {!! Form::text('name', $installment_cycle->name, ['class' => 'form-control', 'placeholder' => __( 'property::lang.installment_cycle'), 'id'
          => 'installment_cycle']); !!}
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
  $('#date').datepicker('setDate', "{{\Carbon::parse($finance_option->date)->format('m/d/y')}}");
</script>