<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.settlement_no' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'petro::lang.date' )) !!}
                    <br>{{@format_datetime($day_entry->settlement_datetime)}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('settlement_no', __( 'petro::lang.settlement_no' )) !!}
                     <br>{{$day_entry->settlement_no}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('user', __( 'petro::lang.user' )) !!}
                     <br>{{$day_entry->username}}
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->