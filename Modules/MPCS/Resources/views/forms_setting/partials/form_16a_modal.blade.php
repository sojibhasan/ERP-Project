<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
<style>
    .9c_table tbody> tr>td{
        padding: 15px;
    }
</style>
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@postForm16ASetting'),
        'method' => 'post' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.16a_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F16A_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
                    {!! Form::text('F16A_form_tdate', !empty($setting->F16A_form_tdate) ? date('m/d/Y', strtotime($setting->F16A_form_tdate)) : null, ['class' => 'form-control', 'id' => 'F16A_settings_tdate',
                    'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F16A_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
                    {!! Form::text('F16A_form_sn', !empty($setting->F16A_form_sn) ? $setting->F16A_form_sn : null, ['class' => 'form-control', 'id' => 'F16A_form_sn',
                    'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <table class="table table-responsive table-bordered 9c_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">@lang('mpcs::lang.total_purchase_price')</th>
                        <th class="text-center">@lang('mpcs::lang.total_sell_price')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: gainsboro;">
                        <td class="text-red"><b>@lang('mpcs::lang.total_pervious_page')</b></td>
                        <td>
                            <input type="text" name="F16A_total_pp" value="@if(!empty($setting)) {{$setting->F16A_total_pp}} @endif" class="form-control input_number" style="width: 60%;  margin-left: 25%;">
                        </td>
                        <td>
                            <input type="text" name="F16A_total_sp" value="@if(!empty($setting)) {{$setting->F16A_total_sp}} @endif" class="form-control input_number" style="width: 60%;  margin-left: 25%;">
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>
            <br>
            <br>
            <br>
            <div class="col-md-12">
                <div class="col-md-4">
                    {!! Form::label('', __('mpcs::lang.total_previous_page_balance_zero'), []) !!}
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('F16A_first_day_after_stock_taking', 1, !empty($setting->F16A_first_day_after_stock_taking) ? $setting->F16A_first_day_after_stock_taking : false, ['class' => 'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                                @lang('mpcs::lang.first_day_after_stock_taking')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="float:left;">
                                {!! Form::checkbox('F16A_first_day_of_next_month', 1, !empty($setting->F16A_first_day_of_next_month) ? $setting->F16A_first_day_of_next_month : false, ['class' => 'input-icheck', 'id' => 'first_day_of_next_month']); !!}
                                @lang('mpcs::lang.first_day_of_next_month')
                            </label> &Tab;
                            {!! Form::select('F16A_first_day_of_next_month_selected', $months_numbers, !empty($setting->F16A_first_day_of_next_month_selected) ? $setting->F16A_first_day_of_next_month_selected : null, ['class' => 'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right: 10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
                        </div>
                        <div class="clearfix"></div>
                        @lang('mpcs::lang.balance_zero_example')
                    </div>
                </div>
            </div>

        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" >@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#F16A_settings_tdate').datepicker();
</script>