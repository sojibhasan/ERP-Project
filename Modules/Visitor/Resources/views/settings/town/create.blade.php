<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberGroupController@store'), 'method' =>
    'post', 'id' => 'member_group_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_member_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __( 'member::lang.date' ),
        'id' => 'member_group_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('member_group', __( 'member::lang.member_group' )) !!}
        {!! Form::text('member_group', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'member::lang.member_group' ), 'id' => 'member_group_name']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_member_group_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#member_group_date').datepicker({
        format: 'mm/dd/yyyy'
    });
</script>