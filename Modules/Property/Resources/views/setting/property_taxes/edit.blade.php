<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyTaxesController@update', $property_tax->id), 'method' =>
    'post', 'id' => 'property_tax_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.add_property_tax' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('location_id', __( 'property::lang.business_location' ) . ':*') !!}
          {!! Form::select('location_id', $business_locations, $property_tax->location_id, ['placeholder' => __( 'messages.please_select' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('tax_name', __( 'property::lang.property_tax_name' ) . ':*') !!}
          {!! Form::text('tax_name', $property_tax->tax_name, ['class' => 'form-control', 'required', 'placeholder' => __(
          'property::lang.property_tax_name' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('tax_type', __( 'property::lang.tax_type' ) . ':*') !!}
          {!! Form::select('tax_type', ['fixed' => __('property::lang.fixed'), 'percentage' =>
          __('property::lang.percentage')], $property_tax->tax_type, ['placeholder' => __( 'messages.please_select' ), 'required', 'class'
          => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12 @if($property_tax->tax_type == 'percentage') hide @endif fixed_div">
          {!! Form::label('value', __( 'property::lang.amount' ) . ':*') !!}
          {!! Form::text('fixed_value', $property_tax->value, ['class' => 'form-control', 'placeholder' => __( 'property::lang.amount'), 'id'
          => 'fixed_value']); !!}
        </div>
        <div class="form-group col-sm-12 @if($property_tax->tax_type == 'fixed') hide @endif percentage_div">
          {!! Form::label('value', __( 'property::lang.percentage' ) . ':*') !!}
          {!! Form::text('percentage_value', $property_tax->value, ['class' => 'form-control', 'placeholder' => __( 'property::lang.percentage'),
          'id' => 'percentage_value']); !!}
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
  $('#tax_type').change(function(){
    if($(this).val() == 'fixed'){
      $('.fixed_div').removeClass('hide');
      $('.percentage_div').addClass('hide');
      $('#fixed_value').prop('required', true);
      $('#percentage_value').prop('required', false);
    } else if ($(this).val() == 'percentage'){
      $('.fixed_div').addClass('hide');
      $('.percentage_div').removeClass('hide');
      $('#fixed_value').prop('required', false);
      $('#percentage_value').prop('required', true);
    }else{
      $('.fixed_div').addClass('hide');
      $('.percentage_div').addClass('hide');
      $('#fixed_value').prop('required', false);
      $('#percentage_value').prop('required', false);
    }
  })
</script>