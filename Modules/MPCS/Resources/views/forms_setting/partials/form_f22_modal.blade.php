<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">
<style>
    .9c_table tbody> tr>td{
        padding: 15px;
    }
</style>
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@postFormF22Setting'),
        'method' => 'post', 'id' => 'f22_settings_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.f22_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F22_no_of_product_per_page', __( 'mpcs::lang.F22_no_of_product_per_page' ) . ':*') !!}
                    {!! Form::text('F22_no_of_product_per_page', !empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : null, ['class' => 'form-control', 'id' => 'F22_no_of_product_per_page',
                    'required', 'placeholder' => __( 'mpcs::lang.enter' ) ]); !!}
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
    $('#F16A_setting_tdate').datepicker().datepicker("setDate", new Date());
</script>