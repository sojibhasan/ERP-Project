<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\TankDipChartController@postImport'),
        'method' => 'post', 'id' => 'tank_dip_chart_add_form', 'enctype' => 'multipart/form-data' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.import_tank_dip_chart' )</h4>
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
                    {!! Form::text('sheet_name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'superadmin::lang.sheet_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_manufacturer', __( 'superadmin::lang.tank_manufacturer' ) . ':*') !!}
                    {!! Form::text('tank_manufacturer', null, ['class' => 'form-control', 'required', 'placeholder' =>
                    __( 'superadmin::lang.tank_manufacturer' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_capacity', __( 'superadmin::lang.tank_capacity' ) . ':*') !!}
                    {!! Form::text('tank_capacity', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'superadmin::lang.tank_capacity' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('unit_id', __( 'superadmin::lang.unit' ) . ':*') !!}
                    {!! Form::select('unit_id', $units,null, ['class' => 'form-control select2', 'required',
                    'placeholder'
                    => __(
                    'superadmin::lang.please_select' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                    {!! Form::file('tank_dip_chart_csv', ['accept'=> '.xls', 'required' => 'required', 'style' =>
                    'margin-top: 5px;']); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <br>
            <div class="col-sm-4">
                <a href="{{ asset('files/import_tank_dip_chart_csv_template.xls') }}" class="btn btn-success"
                    download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'superadmin::lang.import' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#date').datepicker('setDate', new Date());
</script>