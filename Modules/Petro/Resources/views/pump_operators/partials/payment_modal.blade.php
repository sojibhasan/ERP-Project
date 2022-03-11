<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.payment_summary' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    @include('petro::pump_operators.partials.payment_section')
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button value="save" id="payment_submit" name="submit" class="btn btn-primary"
                style="color: #fff; background-color:#2874A6;" type="button">@lang('lang_v1.save') </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script src="{{url('Modules/Petro/Resources/assets/js/po_payment.js')}}"></script>
<script>
    $('#amount').focus();
</script>