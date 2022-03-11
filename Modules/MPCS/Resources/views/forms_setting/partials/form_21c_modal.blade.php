<div class="modal-dialog" role="document" style="width: 75%;">
    <div class="modal-content">
        <style>
            table.table-bordered>thead>tr>th {
                border: 1px solid #222 !important;
            }

            table.table-bordered>tbody>tr>td {
                border: 1px solid #222 !important;
                font-size: 13px;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@postForm21CSetting'),
        'method' => 'post', 'id' => 'F21C_setting_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.21c_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F21C_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
                    {!! Form::text('F21C_form_sn', !empty($settings)? $settings->F21C_form_sn : null, ['class' => 'form-control', 'id' => 'F21C_form_sn',
                    'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F21C_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
                    {!! Form::text('F21C_form_tdate',  !empty($settings)? $settings->F21C_form_tdate : null, ['class' => 'form-control', 'id' => 'F21C_setting_tdate',
                    'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <table class="table table-responsive table-bordered 21c_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2">@lang('mpcs::lang.description')</th>
                        @foreach ($merged_sub_categories as $merged)
                        <th class="text-center" colspan="2">{{$merged->merged_sub_category_name}}</th>
                        @endforeach
                        <th class="text-center" colspan="2">@lang('mpcs::lang.bulk_item')</th>
                        <th class="text-center" colspan="2">@lang('mpcs::lang.total')</th>
                    </tr>
                    <tr>
                        @foreach ($merged_sub_categories as $merged)
                        <th class="text-center">@lang('mpcs::lang.balance_qty')</th>
                        <th class="text-center">@lang('mpcs::lang.value')</th>
                        @endforeach
                        <th class="text-center">@lang('mpcs::lang.balance_qty')</th>
                        <th class="text-center">@lang('mpcs::lang.value')</th>
                        <th class="text-center">@lang('mpcs::lang.balance_qty')</th>
                        <th class="text-center">@lang('mpcs::lang.value')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="11" class="text-red"><b>@lang('mpcs::lang.receipts')</b></td>
                    </tr>
                    <tr>
                        <td><b>@lang('mpcs::lang.previous_day')</b></td>
                        @foreach ($merged_sub_categories as $merged)
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                    <tr>
                        <td><b>@lang('mpcs::lang.opening_stock')</b></td>
                        @foreach ($merged_sub_categories as $merged)
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                    </tr>
                    <tr>
                        <td><b>@lang('mpcs::lang.price_increment_pre_date')</b></td>
                        @foreach ($merged_sub_categories as $merged)
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" class="text-red"><b>@lang('mpcs::lang.issues')</b></td>
                    </tr>
                    <tr>
                        <td><b>@lang('mpcs::lang.previous_day')</b></td>
                        @foreach ($merged_sub_categories as $merged)
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                    </tr>
                    <tr>
                        <td><b>@lang('mpcs::lang.price_reduction_pre_date')</b></td>
                        @foreach ($merged_sub_categories as $merged)
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
                        </td>
                        <td>
                            <input type="text" name="" class="form-control input_number" style="width: 80%%;">
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
                                {!! Form::checkbox('F21C_first_day_after_stock_taking', 1, !empty($settings)? $settings->F21C_first_day_after_stock_taking : false, ['class' => 'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                                @lang('mpcs::lang.first_day_after_stock_taking')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="float:left;">
                                {!! Form::checkbox('F21C_first_day_of_next_month', 1, !empty($settings)? $settings->F21C_first_day_of_next_month : false, ['class' => 'input-icheck', 'id' => 'first_day_of_next_month']); !!}
                                @lang('mpcs::lang.first_day_of_next_month')
                            </label> &Tab;
                            {!! Form::select('F21C_first_day_of_next_month_selected', $months, !empty($settings)? $settings->F21C_first_day_of_next_month_selected : null, ['class' => 'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right: 10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
                        </div>
                        <div class="clearfix"></div>
                        @lang('mpcs::lang.balance_zero_example')
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
     $('#F21C_setting_tdate').datepicker({
        format: 'mm/dd/yyyy'
    });
    $('#F21C_setting_tdate').datepicker("setDate", "{{!empty($settings->F21C_form_tdate) ? \Carbon::parse($settings->F21C_form_tdate)->format('m/d/Y') : date('m/d/Y')}}");
   
</script>