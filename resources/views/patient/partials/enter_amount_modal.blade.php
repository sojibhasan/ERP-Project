<div class="modal-dialog" role="document" style="width: 40%" >
    <div class="modal-content print">
        <div class="modal-header">
            <h5 class="modal-title">@lang('patient.enter_amount')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="row">
            
            <div class="col-md-6 col-md-offset-3">
                {!! Form::hidden('id', $id,['id' => 'id']) !!}
                <div class="form-group">
                    {!! Form::label('enter_amount', __('patient.amount') . ':') !!}
                    {!! Form::text('enter_amount', !empty($amount)? $amount : null, ['class' => 'form-control',  'placeholder' =>  __('patient.amount'), 'id' => 'prescription_amount']) !!}
    
                </div>
            </div>

        </div>

        <div class="row" style="margin: 20px; padding-bottom:20px;">
            <button class="btn btn-primary pull-right" id="prescription_amount_submit"
                type="submit">@lang('messages.submit')</button>
        </div>
    </div>
</div>

<script>
    $('#prescription_amount_submit').click(function(){
        id = $('#id').val();
        enter_amount = $('#prescription_amount').val();
        $.ajax({
            method: 'post',
            url: '{{$action}}',
            data: { amount: enter_amount, id: id },
            success: function(result) {
                console.log(result);
                if(result.success == 1){
                    toastr.success(result.msg);
                    {{$table}}.ajax.reload();
                }else{
                    toastr.error(result.msg);
                }
                $('.view_modal').modal('hide');
            },
        });
    });
</script>
