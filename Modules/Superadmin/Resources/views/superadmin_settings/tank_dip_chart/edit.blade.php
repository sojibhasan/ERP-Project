<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\TankDipChartController@update', $tank_dip_chart->id), 'method'
        => 'put', 'id' => 'tank_dip_chart_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit_tank_dip_chart' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'superadmin::lang.date' ) . ':*') !!}
                    {!! Form::text('date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' =>
                    __(
                    'superadmin::lang.date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sheet_name', __( 'superadmin::lang.sheet_name' ) . ':*') !!}
                    {!! Form::text('sheet_name',  $tank_dip_chart->sheet_name, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'superadmin::lang.sheet_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_manufacturer', __( 'superadmin::lang.tank_manufacturer' ) . ':*') !!}
                    {!! Form::text('tank_manufacturer',  $tank_dip_chart->tank_manufacturer, ['class' => 'form-control', 'required', 'placeholder' =>
                    __( 'superadmin::lang.tank_manufacturer' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_capacity', __( 'superadmin::lang.tank_capacity' ) . ':*') !!}
                    {!! Form::text('tank_capacity',  $tank_dip_chart->tank_capacity, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'superadmin::lang.tank_capacity' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('unit_id', __( 'superadmin::lang.unit' ) . ':*') !!}
                    {!! Form::select('unit_id', $units, $tank_dip_chart->unit_id, ['class' => 'form-control select2', 'required', 'placeholder'
                    => __(
                    'superadmin::lang.please_select' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('dummay_dip_reading', __( 'superadmin::lang.dip_reading' ) . ':*') !!}
                    {!! Form::text('dummay_dip_reading', null, ['class' => 'form-control', 'placeholder' =>
                    __(
                    'superadmin::lang.dip_reading' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('dummay_dip_reading_value', __( 'superadmin::lang.dip_reading_value' ) . ':*') !!}
                    {!! Form::text('dummay_dip_reading_value', null, ['class' => 'form-control',
                    'placeholder' => __(
                    'superadmin::lang.dip_reading_value' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary add_dip_reading" type="button"
                    style="margin-top: 23px;">@lang('superadmin::lang.add')</button>
            </div>
            <br>
            <div class="col-md-12">
                <table class="table" id="dip_reading_table">
                    <thead>
                        <tr>
                            <th>@lang('superadmin::lang.dip_reading')</th>
                            <th>@lang('superadmin::lang.dip_reading_value')</th>
                            <th>@lang('superadmin::lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $row_id = 0;
                        @endphp
                        @foreach ($tank_dip_cahrt_details as $item)
                        <tr class="row_{{$row_id}}">
                            <td>{{$item->dip_reading}}</td>
                            <td>{{$item->dip_reading_value}}</td>
                            <td><button type="button" class="btn btn-danger btn-xs remove_row" data-row_id="{{$row_id}}"><i class="fa fa-times"></i></button></td>
                            <input type="hidden" name="dip_readings[{{$row_id}}][reading]" value="{{$item->dip_reading}}">
                            <input type="hidden" name="dip_readings[{{$row_id}}][value]" value="{{$item->dip_reading_value}}">
                        </tr>
                        @php
                            $row_id++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" id="row_id" value="{{$row_id}}">
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
    $('#date').datepicker('setDate', "{{\Carbon::parse($tank_dip_chart->date)->format('m/d/y')}}");

    $(document).on('click', '.add_dip_reading', function(){
        let dummay_dip_reading = $('#dummay_dip_reading').val();
        let dummay_dip_reading_value = $('#dummay_dip_reading_value').val();
        console.log(dummay_dip_reading);
        console.log(dummay_dip_reading_value);
        let row_id = $('#row_id').val();
        if(dummay_dip_reading !== null && dummay_dip_reading !== '' && dummay_dip_reading !== undefined && dummay_dip_reading_value !== null && dummay_dip_reading_value !== '' && dummay_dip_reading_value !== undefined){
            $('#dip_reading_table > tbody').append(
                `<tr class="row_${row_id}">
                    <td>${dummay_dip_reading}</td>
                    <td>${dummay_dip_reading_value}</td>
                    <td><button type="button" class="btn btn-danger btn-xs remove_row" data-row_id="${row_id}"><i class="fa fa-times"></i></button></td>
                    <input type="hidden" name="dip_readings[${row_id}][reading]" value="${dummay_dip_reading}">
                    <input type="hidden" name="dip_readings[${row_id}][value]" value="${dummay_dip_reading_value}">
                </tr>
                
                `
            );
            $('#dummay_dip_reading').val('');
            $('#dummay_dip_reading_value').val('');
            $('#row_id').val(row_id+1);
        }



    })

    $(document).on('click', '.remove_row', function(){
        let row_id = $(this).data('row_id');
        $('#dip_reading_table > tbody').find('.row_' + row_id).remove();

    });
</script>