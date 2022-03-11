<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\SalesOfficerController@store'), 'method' =>
    'post', 'id' => $quick_add ? 'quick_add_sales_officer_form' : 'sales_officer_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.add_sales_officer' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
     
        <div class="form-group col-sm-12">
          {!! Form::label('sale_officer', __( 'property::lang.sale_officer' ) . ':*') !!}
          {!! Form::select('officer_id', $users, null, ['class' => 'form-control select2', 'placeholder' => __( 'property::lang.please_select'), 'id'
          => 'sales_officer_name', 'style' => 'width: 100%']); !!}
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
  $('.select2').select2();
 $('#date').datepicker('setDate', new Date());
 $('#location_id option:eq(1)').attr('selected', 'selected');
</script>