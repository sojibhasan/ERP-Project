<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumperDayEntryController@postAddSettlementNo',
        $id), 'method' => 'post', 'id' =>
        'edit_pumper_day_entry' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_settlement_no' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'petro::lang.date' ) . ':*') !!}
                    {!! Form::text('date', @format_datetime(date('Y-m-d H:i:s')), ['class' => 'form-control date', 'required', 'disabled',
                    'placeholder' => __(
                    'petro::lang.date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('settlement_no', __( 'petro::lang.settlement_no' ) . ':*') !!}
                    {!! Form::text('settlement_no', null, ['class' => 'form-control fuel_tank_date',
                    'required', 'placeholder' => __(
                    'petro::lang.settlement_no' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('#closing_meter').change(function () {
            let sale_price = parseFloat($('#sale_price').val());
            let closing_meter = parseFloat($(this).val());
            let starting_meter = parseFloat($('#starting_meter').val());
            let testing_ltr = parseFloat($('#testing_ltr').val());
            if(closing_meter < starting_meter){
                    toastr.error('Closing meter value should not less then starting meter value');
                    return false;
                }
            let sold_ltr = closing_meter - starting_meter - testing_ltr;
            $('#sold_ltr').val(sold_ltr);
            let total_amount = sale_price * sold_ltr;
            __write_number($('#amount'), total_amount);
        });
    </script>