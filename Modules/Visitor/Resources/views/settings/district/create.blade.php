<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\GramasevaVasamaController@store'), 'method' =>
    'post', 'id' => 'gramaseva_vasama_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_gramaseva_vasama' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'gramaseva_vasama_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('gramaseva_vasama', __( 'member::lang.gramaseva_vasama' )) !!}
        {!! Form::text('gramaseva_vasama', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.gramaseva_vasama' ), 'id' => 'gramaseva_vasama_name']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_gramaseva_vasama_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#gramaseva_vasama_date').datepicker({
        format: 'mm/dd/yyyy'
    });
</script>