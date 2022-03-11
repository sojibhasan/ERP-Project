<style>
    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 75px;
        width: 75px;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }
</style>
<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">

        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@store'), 'method' =>
        'post',
        'id' =>
        'receive_pump_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.receive_pump' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-8">

                        <div class="col-md-4 text-red">
                            <h4>@lang('petro::lang.date_and_time'): {{@format_datetime(date('Y-m-d H:i:s'))}}</h4>
                        </div>
                        <div class="col-md-4 text-red">
                            <h4>@lang('petro::lang.pump_name'): {{$pump->pump_name}}</h4>
                        </div>
                        <div class="col-md-4 text-red">
                            <h4>@lang('petro::lang.product'): {{$pump->name}}</h4>
                        </div>
                        <div class="clearfix"></div>
                        <br>

                        <input type="hidden" name="pump_id" value="{{$pump->id}}">
                        <input type="hidden" name="pump_operator_id" value="{{$pump_operator_id}}">

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('starting_meter', __( 'petro::lang.starting_meter' ) . ':*') !!}
                                {!! Form::text('starting_meter', $pump->pod_last_meter, ['class' =>
                                'form-control input-lg
                                starting_meter', 'required', 'readonly',
                                'placeholder' => __(
                                'petro::lang.starting_meter' ) ]); !!}
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('closing_meter', __( 'petro::lang.reconfirm_meter' ) . ':*') !!}
                                {!! Form::text('closing_meter', null, ['class' => 'form-control input-lg
                                closing_meter', 'min' => $pump->pod_last_meter, 'required',
                                'placeholder' => __(
                                'petro::lang.closing_meter' ) ]); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', __( 'petro::lang.status' ) . ':*') !!} <br>
                                <input type="checkbox" checked name="status" id="toggle-two" data-toggle="toggle">
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
                                            <button id="0" type="button" class="btn"
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
            <br>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary confirm_meter_reading_btn">@lang(
                    'petro::lang.confirm_meter_reading' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('#toggle-two').bootstrapToggle({
            on: 'Open',
            off: 'Close',
            width: 100,
            onstyle: 'success',
            offstyle: 'danger'
        });

        
    </script>

    <script>
        $('.confirm_meter_reading_btn').attr('disabled', true);
        $('.pump_operator_modal ').on('shown.bs.modal', function () {
            $('#closing_meter').focus();
        }) 
        $('.confirm_meter_reading_btn').click(function(){
            $('#receive_pump_form').validate();
        })
        $('#receive_pump_form').validate();
        function enterVal(val) {
            if(val === 'precision'){
                str = $('#closing_meter').val();
                str = str + '.';
                $('#closing_meter').val(str);
                return;
            }
            if(val === 'backspace'){
                str = $('#closing_meter').val().replace(',', '');
                str = str.substring(0, str.length - 1);
                $('#closing_meter').val(str);
                return;
            }
            let closing_meter = $('#closing_meter').val().replace(',', '') + val;
            $('#closing_meter').val(closing_meter);
            $('#closing_meter').focus();
            $('#closing_meter').keyup();
        };

        $('#closing_meter').keyup(function () {
            if(parseFloat($(this).val()) === parseFloat($('#starting_meter').val())){
                $('.confirm_meter_reading_btn').attr('disabled', false);
            }else{
                $('.confirm_meter_reading_btn').attr('disabled', true);
            }
        })

        $('#toggle-two').change(function () {
            if($(this).prop("checked") === false){
                $('.confirm_meter_reading_btn').attr('disabled', false);
            }else{
                $('.confirm_meter_reading_btn').attr('disabled', true);
                $('#closing_meter').trigger('change');
            }
        })

    </script>