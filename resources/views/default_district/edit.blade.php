<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('DefaultDistrictController@update',$districts->id), 'method' => 'PUT', 'id' =>
        'edit_districts_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'visitors.edit_districts' )</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                {!! Form::text('name', $districts->name, ['class' => 'form-control', 'required','placeholder' => __(
                'lang_v1.name' ) ]); !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-submit">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
     $('#account_type_id').change(function(){
          var this_val = parseInt($(this).val());
            $.ajax({
                method: 'get',
                url: '/default-account-group/get-account-groups-by-type/'+this_val,
                data: {  },
                contentType : 'html',
                success: function(result) {
                    $('#asset_type').empty().append(result);
                },
            });
      });
</script>