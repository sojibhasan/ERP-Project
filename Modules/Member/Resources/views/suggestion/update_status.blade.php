<div class="modal-dialog" role="document">
    <div class="modal-content">

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\SuggestionController@postUpdateStatus', $suggestion->id), 'method' =>
        'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{$suggestion->heading}}</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('status', __( 'member::lang.status' )) !!}
                    {!! Form::select('status', $status_array, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'member::lang.please_select' ), 'id' => 'status']);
                    !!}
                </div>
            </div>
            <div class="col-md-6 hide assigned_to_member">
                <div class="form-group">
                    {!! Form::label('assigned_to_member_id', __( 'member::lang.member' )) !!}
                    {!! Form::select('assigned_to_member_id', $members, null, ['class' => 'form-control select2',
                    'placeholder' => __('member::lang.please_select' )]);
                    !!}
                </div>
            </div>



        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_suggestion_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   $('#status').change(function(){
        if($(this).val() == 'assigned_to'){
            $('.assigned_to_member').removeClass('hide');
        }else{
            $('.assigned_to_member').addClass('hide');
        }
   })
</script>