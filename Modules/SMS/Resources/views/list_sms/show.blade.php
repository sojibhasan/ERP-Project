<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'sms::lang.view_sms' )</h4>
        </div>

        <div class="modal-body">

            <div class="col-md-4">
                {!! Form::label('group', __( 'sms::lang.date' )) !!} {{$sms->sent_on}}
            </div>
            <div class="col-md-12">
                <label for="message"> @lang( 'sms::lang.message' )</label> 
                {!! Form::textarea('mesage', $sms->message, ['class' => 'form-control', 'rows' => '5', 'disables']) !!}
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="col-md-4">
                {!! Form::label('member_group', __( 'sms::lang.member_group' )) !!} {{$sms->member_group}}
            </div>
            <div class="col-md-4">
                {!! Form::label('balamandala', __( 'sms::lang.balamandala' )) !!} {{$sms->balamandala}}
            </div>
            <div class="col-md-4">
                {!! Form::label('gramseva_vasama', __( 'sms::lang.gramseva_vasama' )) !!} {{$sms->gramseva_vasama}}
            </div>
            <div class="col-md-4">
                {!! Form::label('user', __( 'sms::lang.user' )) !!} {{$sms->user}}
            </div>

        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>