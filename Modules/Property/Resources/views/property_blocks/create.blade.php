<div class="modal-dialog" role="document" style="width: 45%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyBlocksController@store'), 'method' =>
    'post', 'id' => 'blocks_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.add_blocks' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group ">
            {!! Form::label('transaction_date', __( 'property::lang.transaction_date' )) !!}
            {!! Form::text('transaction_date', null, ['class' => 'form-control', 'required', 'placeholder' => __(
            'property::lang.name' )]); !!}
          </div>
        </div>
        <input type="hidden" name="property_id" value="{{$property->id}}">
        <div class="col-md-6">
          <div class="form-group ">
            {!! Form::label('land_name', __( 'property::lang.land_name' )) !!}
            {!! Form::text('land_name', $property->name, ['class' => 'form-control', 'placeholder' => __(
            'property::lang.land_name' ), 'required', 'readonly']); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            {!! Form::label('block_number', __( 'property::lang.block_number' )) !!}
            {!! Form::text('block_number', null, ['class' => 'form-control', 'placeholder' => __(
            'property::lang.block_number' ), 'required']); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group ">
            {!! Form::label('block_sale_price', __( 'property::lang.block_sale_price' )) !!}
            {!! Form::text('block_sale_price', null, ['class' => 'form-control', 'placeholder' => __(
            'property::lang.block_sale_price' ), 'required']); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group ">
            {!! Form::label('block_extent', __( 'property::lang.block_extent' )) !!}
            {!! Form::text('block_extent', null, ['class' => 'form-control', 'placeholder' => __(
            'property::lang.block_extent' ), 'required']); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('unit_id', __('property::lang.property_unit') . ':') !!}
            {!! Form::select('unit_id', $units, $unitid, ['class' => 'form-control select2', 'style' => 'width:100%',
            'placeholder' => __('lang_v1.please_select')]); !!}
          </div>
        </div>
        <div class="col-md-12">
          <button type="button" class="btn btn-primary pull-right add_block_list">@lang('property::lang.add')</button>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-12 repeat_field" style="margin-top: 10px;">
          <table class="table table-bordered table-striped" id="block_list_table">
            <thead>
              <tr>
                <th>@lang('property::lang.block_number')</th>
                <th>@lang('property::lang.block_extent')</th>
                <th>@lang('property::lang.units')</th>
                <th>@lang('property::lang.block_sale_price')</th>

              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="input_value "></div>

      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default add_block_btn" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#transaction_date').datepicker('setDate', new Date());

  $('.block_date').datepicker();
        var index = 0;
        $('.add_block_list').click(function(){
            $('#block_list_table tbody').append(`
            <tr class="tr_`+index+`">
              <td>`+$('#block_number').val()+`</td>
              <td>`+$('#block_extent').val()+`</td>
              <td>`+$('#unit_id :selected').text()+`</td>
              <td>`+$('#block_sale_price').val()+`</td>
              <td><button type="button" class="btn btn-xs btn-danger remove_row" style="margin-top: 2px;" data-rowid="`+index+`"><i class="fa fa-times"></i></button></td>
            </tr>
            `);
            $('.input_value').append(`
           <input class="input_`+index+`" type="hidden" name="blocks[`+index+`][block_number]" value="`+$('#block_number').val()+`">
           <input class="input_`+index+`" type="hidden" name="blocks[`+index+`][block_extent]" value="`+$('#block_extent').val()+`">
           <input class="input_`+index+`" type="hidden" name="blocks[`+index+`][unit_id]" value="`+$('#unit_id').val()+`">
           <input class="input_`+index+`" type="hidden" name="blocks[`+index+`][block_sale_price]" value="`+$('#block_sale_price').val()+`">
            `);
            index = index +1;
            $('#block').val('');
        });

        $(document).on('click', 'button.remove_row', function(){
            rowid = $(this).data('rowid');
            
            $('.tr_'+rowid).remove();
            $('.input_'+rowid).remove();
        });

</script>