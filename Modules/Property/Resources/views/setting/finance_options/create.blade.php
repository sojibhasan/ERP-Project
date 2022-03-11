<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\FinanceOptionController@store'), 'method' =>
    'post', 'id' => $quick_add ? 'quick_add_finance_option_form' : 'finance_option_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.add_finance_option' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('location_id', __( 'property::lang.business_location' ) . ':*') !!}
          {!! Form::select('location_id', $business_locations, null, ['placeholder' => __( 'messages.please_select' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('finance_option', __( 'property::lang.finance_option' ) . ':*') !!}
          {!! Form::text('finance_option', null, ['class' => 'form-control', 'placeholder' => __( 'property::lang.finance_option'), 'id'
          => 'finance_option']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('custom_payments', __( 'property::lang.custom_payments' ) . ':*') !!}
          {!! Form::select('custom_payments', ['no' => 'No', 'yes' => 'Yes'], null, ['placeholder' => __( 'messages.please_select' ),
          'required', 'class' => 'form-control']); !!}
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
 $('#date').datepicker('setDate', new Date());
 $('#location_id option:eq(1)').attr('selected', 'selected');
</script>