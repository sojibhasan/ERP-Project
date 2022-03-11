<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\BalamandalayaController@store'), 'method' =>
    'post', 'id' => 'balamandalaya_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_balamandalaya' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'balamandalaya_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('gramaseva_vasama', __( 'member::lang.gramaseva_vasama' )) !!}
        {!! Form::select('gramaseva_vasama_id', $gramaseva_vasamas ,null, ['class' => 'form-control select2',
        'required', 'placeholder' => __( 'member::lang.please_select' ), 'id' => 'gramaseva_vasama_id', 'style' =>
        'width:100%']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('balamandalaya', __( 'member::lang.balamandalaya' )) !!}
        {!! Form::text('balamandalaya', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.balamandalaya' ), 'id' => 'balamandalaya_name']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_balamandalaya_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#balamandalaya_date').datepicker({
        format: 'mm/dd/yyyy'
    });
   $('.select2').select2();
</script>