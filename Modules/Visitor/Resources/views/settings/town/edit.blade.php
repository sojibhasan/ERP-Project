<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberGroupController@update',
    $member_group->id), 'method' => 'PUT', 'id' => 'member_group_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_member_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'member::lang.date' )) !!}
        {!! Form::text('date', \Carbon::parse($member_group->date)->format('m/d/Y'), ['class' => 'form-control',
        'required', 'placeholder' => __( 'member::lang.date' ), 'id' => 'member_group_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('member_group', __( 'member::lang.member_group' )) !!}
        {!! Form::text('member_group', $member_group->member_group, ['class' => 'form-control',
        'required', 'placeholder' => __( 'member::lang.member_group' ), 'id' => 'member_group_name']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_member_group_btn">@lang( 'member::lang.update'
        )</button>
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