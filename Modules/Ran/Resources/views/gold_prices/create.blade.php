<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\GoldPriceController@store'), 'method' =>
      'post', 'id' => 'gold_price_form' ])
      !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'ran::lang.add_gold_price' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('date_and_time', __( 'ran::lang.date_and_time' )) !!}
            {!! Form::text('date_and_time', date('m-d-Y H:i:s'), ['class' => 'form-control short', 'id' =>
            'date_and_time', 'placeholder' => __( 'ran::lang.date_and_time' )]);
            !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('grade_name', __( 'ran::lang.grade_name' )) !!}
            {!! Form::text('grade_name', !empty( $last_grade) ?  $last_grade->grade_name : null, ['class' => 'form-control short', 'readonly', 'id' =>
            'grade_name', 'placeholder' => __( 'ran::lang.grade_name' )]);
            !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('gold_purity', __( 'ran::lang.gold_purity' )) !!}
            {!! Form::text('purity', !empty($last_grade) ? $last_grade->gold_purity : null, ['class' => 'form-control short input_number', 'readonly', 'id' =>
            'gold_purity', 'placeholder' => __( 'ran::lang.gold_purity' )]);
            !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('price', __( 'ran::lang.rate_per_gram' )) !!}
            {!! Form::text('price', null, ['class' => 'form-control short input_number', 'id' =>
            'price', 'placeholder' => __( 'ran::lang.rate_per_gram' )]);
            !!}
          </div>
        </div>
      </div>
      <input type="hidden" name="grade_id" value="{{!empty($last_grade) ? $last_grade->id : null}}">
      <div class="clearfix"></div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="save_gold_grade_btn">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
    $('#date_and_time').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });
  </script>