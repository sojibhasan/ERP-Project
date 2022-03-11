<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <style>
            .9c_table tbody>tr>td {
                padding: 15px;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@postForm9CSetting'),
        'method' => 'post', 'id' => 'F9C_settings_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.9c_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F9C_setting_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
                    {!! Form::text('F9C_setting_tdate', null, ['class' => 'form-control',
                    'id' => 'F9C_setting_tdate',
                    'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F9C_setting_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
                    {!! Form::text('F9C_setting_sn',!empty($settings->F9C_sn) ? $settings->F9C_sn : null, ['class' =>
                    'form-control', 'id' => 'F9C_setting_sn',
                    'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered 9c_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center"></th>
                            @foreach ($sub_categories as $sub_cat)
                            <th class="text-center" colspan="2" style="width: 100px;">{{$sub_cat->name}}</th>
                            @endforeach
                        </tr>
                        <br>
                        <tr>
                            <th></th>
                            @foreach ($sub_categories as $sub_cat)
                            <th class="text-center" style="padding: 30px;">@lang('mpcs::lang.qty')</th>
                            <th class="text-center" style="padding: 30px;">@lang('mpcs::lang.amount')</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background: gainsboro;">
                            <td class="text-red"><b>@lang('mpcs::lang.total_pervious_page')</b></td>
                            @foreach ($sub_categories as $sub_cat)
                            @php
                            $form_9c_sub_cat = \Modules\MPCS\Entities\Form9cSubCategory::where('business_id',
                            $business_id)->where('sub_category_id', $sub_cat->id)->first();
                            @endphp
                            <td>
                                <input type="text" name="sub_cat_9c[{{$sub_cat->id}}][qty]"
                                    value="{{!empty($form_9c_sub_cat->qty) ? $form_9c_sub_cat->qty : 0.00}}" class="form-control input_number" style="width: 100%;">
                            </td>
                            <td>
                                <input type="text" name="sub_cat_9c[{{$sub_cat->id}}][amount]"
                                    value="{{!empty($form_9c_sub_cat->amount) ? $form_9c_sub_cat->amount : 0.00}}" class="form-control input_number" style="width: 100%;">
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
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
                                {!! Form::checkbox('F9C_first_day_after_stock_taking', 1, !empty($settings) ?
                                $settings->F9C_first_day_after_stock_taking : 0, ['class' =>
                                'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                                @lang('mpcs::lang.first_day_after_stock_taking')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="float:left;">
                                {!! Form::checkbox('F9C_first_day_of_next_month', 1, !empty($settings) ?
                                $settings->F9C_first_day_of_next_month : 0, ['class' => 'input-icheck',
                                'id' => 'first_day_of_next_month']); !!}
                                @lang('mpcs::lang.first_day_of_next_month')
                            </label> &Tab;
                            {!! Form::select('F9C_first_day_of_next_month_selected', $months, !empty($settings) ?
                            $settings->F9C_first_day_of_next_month_selected : null, ['class' =>
                            'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right:
                            10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
                        </div>
                        <div class="clearfix"></div>
                        @lang('mpcs::lang.balance_zero_example')
                    </div>
                </div>
            </div>

        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="F9C_settings_form_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#F9C_setting_tdate').datepicker({
        format: 'mm/dd/yyyy'
    });
    $('#F9C_setting_tdate').datepicker("setDate", "{{!empty($settings->F9C_setting_tdate) ? \Carbon::parse($settings->F9C_setting_tdate)->format('m/d/Y') : date('m/d/Y')}}");
</script>