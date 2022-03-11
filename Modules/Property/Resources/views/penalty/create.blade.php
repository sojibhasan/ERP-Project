<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PenaltyController@store'),
    'method' =>
    'post', 'id' => 'penalty_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.penalty' )</h4>
    </div>

    <div class="modal-body">
      <input type="hidden" name="sell_line_id" id="sell_line_id" value="{{$sell_line_id}}">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly',
          'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('amount', __( 'property::lang.amount' ) . ':*') !!}
          {!! Form::text('amount', '', ['class' => 'form-control','placeholder' =>
          __('property::lang.amount')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('note', __( 'property::lang.note' ) . ':*') !!}
          {!! Form::textarea('note', '', ['class' => 'form-control', 'rows' => 3, 'placeholder' =>
          __('property::lang.note')]); !!}
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
</script>