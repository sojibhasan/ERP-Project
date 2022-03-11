<style>
    .side-label {
        font-size: 21px;
        font-weight: bold;
        padding-top: 5px;
    }

    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 80px;
        width: 80px;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }
</style>
<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\UnloadStockController@store'),
        'method' =>
        'post',
        'id' =>
        'unload_stock_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.closing_meter' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('tank_id', __( 'petro::lang.tank' ) . ':*') !!}
                                    {!! Form::select('tank_id', $tanks,
                                    null , ['class' => 'form-control select2
                                    tank_id',
                                    'placeholder' => __(
                                    'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('product', __( 'petro::lang.product' ) . ':*') !!}
                                    {!! Form::text('product', null, ['class' => 'form-control product', 'required',
                                    'placeholder' => __(
                                    'petro::lang.product' ) ]); !!}
                                </div>
                            </div>
                            <input type="hidden" name="product_id" id="product_id">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('unloaded_qty', __( 'petro::lang.unloaded_qty' ) . ':*') !!}
                                    {!! Form::text('unloaded_qty', null, ['class' => 'form-control unloaded_qty',
                                    'required',
                                    'placeholder' => __(
                                    'petro::lang.unloaded_qty' ) ]); !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bill_no', __( 'petro::lang.bill_no' ) . ':*') !!}
                                    {!! Form::text('bill_no', null, ['class' => 'form-control bill_no', 'required',
                                    'placeholder' => __(
                                    'petro::lang.bill_no' ) ]); !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}
                                    {!! Form::text('dip_reading', null, ['class' => 'form-control dip_reading',
                                    'required',
                                    'placeholder' => __(
                                    'petro::lang.dip_reading' ) ]); !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('current_stock', __( 'petro::lang.current_stock' ) . ':*') !!}
                                    {!! Form::text('current_stock', null, ['class' => 'form-control current_stock',
                                    'required',
                                    'placeholder' => __(
                                    'petro::lang.current_stock' ) ]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div id="key_pad" tabindex="1">
                                    <div class="row text-center" id="calc">
                                        <div class="calcBG col-md-12 text-center">
                                            <div class="row">
                                                <button id="7" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">7</button>
                                                <button id="8" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">8</button>
                                                <button id="9" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">9</button>

                                            </div>
                                            <div class="row">
                                                <button id="4" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">4</button>
                                                <button id="5" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">5</button>
                                                <button id="6" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">6</button>
                                            </div>
                                            <div class="row">
                                                <button id="1" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">1</button>
                                                <button id="2" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">2</button>
                                                <button id="3" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">3</button>
                                            </div>
                                            <div class="row">
                                                <button id="backspace" type="button" class="btn btn-danger"
                                                    onclick="enterVal(this.id)">⌫</button>
                                                <button id="0" type="button" class="btn btn-primary btn-sm"
                                                    onclick="enterVal(this.id)">0</button>
                                                <button id="precision" type="button" class="btn btn-success"
                                                    onclick="enterVal(this.id)">.</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#tank_id').change(function () {
    $.ajax({
        method: 'get',
        url: '/petro/get-tank-product/'+$(this).val(),
        data: {  },
        success: function(result) {
            console.log(result);
            $('#product').val(result.product.name);
            $('#product_id').val(result.product.id);
        },
    });
})
</script>