<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.edit_stamp')</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('Chequer\ChequerStampController@update', $stamp->id), 'method' => 'put', 'enctype'
            => 'multipart/form-data']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('stamp_name', __('cheque.stamp_name') . ':') !!}
                        <div class="input-group">
                            {!! Form::text('stamp_name', $stamp->stamp_name, ['class' => 'form-control', 'placeholder' =>
                            __('cheque.stamp_name'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('upload_stamp', __('cheque.upload_stamp') . ':') !!}
                        <div class="input-group">
                            {!! Form::file('upload_stamp', null, ['class' => 'form-control', 'placeholder' =>
                            __('cheque.upload_stamp'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('active', 1, $stamp->stamp_status, ['class' => 'input-icheck', 'id' => 'active']);
                            !!}
                            @lang('cheque.active')
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                    data-dismiss="modal">Close</button>
                <button type="submit" style="margin-right: 5px;"
                    class="pull-right btn btn-primary">@lang('cheque.update_stamp')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>