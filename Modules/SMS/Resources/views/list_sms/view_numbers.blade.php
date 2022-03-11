<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'sms::lang.view_numbers' )</h4>
        </div>

        <div class="modal-body">

            <div class="col-md-4">
                {!! Form::label('numbers', __( 'sms::lang.numbers' )) !!} :
                <br>
                @foreach ($numbers as $number)
                {{$number}} <br>
                @endforeach
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