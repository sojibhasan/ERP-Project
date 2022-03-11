<div class="pos-tab-content active">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name',__('business.business_name') . ':*') !!}
                {!! Form::text('name', $business->name, ['class' => 'form-control', 'required', 'readonly',
                'placeholder' => __('business.business_name')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('start_date', __('business.start_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    
                    {!! Form::text('start_date', @format_date($business->start_date), ['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_profit_percent', __('business.default_profit_percent') . ':*') !!} @if(!empty($help_explanations['default_profit_percent'])) @show_tooltip($help_explanations['default_profit_percent']) @endif
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-plus-circle"></i>
                    </span>
                    {!! Form::text('default_profit_percent', @num_format($business->default_profit_percent), ['class' => 'form-control input_number']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('currency_symbol_placement', __('lang_v1.currency_symbol_placement') . ':') !!}
                {!! Form::select('currency_symbol_placement', ['before' => __('lang_v1.before_amount'), 'after' => __('lang_v1.after_amount')], $business->currency_symbol_placement, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('time_zone', __('business.time_zone') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    {!! Form::select('time_zone', $timezone_list, $business->time_zone, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('business_logo', __('business.upload_logo') . ':') !!}
                    {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
                    <p class="help-block"><i> @lang('business.logo_help')</i></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('fy_start_month', __('business.fy_start_month') . ':') !!}@if(!empty($help_explanations['financial_year_start_month'])) @show_tooltip($help_explanations['financial_year_start_month']) @endif
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('fy_start_month', $months, $business->fy_start_month, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('accounting_method', __('business.accounting_method') . ':*') !!}
                @if(!empty($help_explanations['stock_accounting_method'])) @show_tooltip($help_explanations['stock_accounting_method']) @endif
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calculator"></i>
                    </span>
                    {!! Form::select('accounting_method', $accounting_methods, $business->accounting_method, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('transaction_edit_days', __('business.transaction_edit_days') . ':*') !!}
                @if(!empty($help_explanations['transaction_edit_days'])) @show_tooltip($help_explanations['transaction_edit_days']) @endif
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-edit"></i>
                    </span>
                    {!! Form::number('transaction_edit_days', $business->transaction_edit_days, ['class' => 'form-control','placeholder' => __('business.transaction_edit_days'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('date_format', __('lang_v1.date_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('date_format', $date_formats, $business->date_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('time_format', __('lang_v1.time_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    {!! Form::select('time_format', [12 => __('lang_v1.12_hour'), 24 => __('lang_v1.24_hour')], $business->time_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('currency_precision',__('business.currency_precision') . ':*') !!}
                {!! Form::number('currency_precision', !empty($business->currency_precision) ? $business->currency_precision : 2, ['class' => 'form-control',
                'placeholder' => __('business.currency_precision')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('quantity_precision',__('business.quantity_precision') . ':*') !!}
                {!! Form::number('quantity_precision', !empty($business->quantity_precision) ? $business->quantity_precision : 0, ['class' => 'form-control',
                'placeholder' => __('business.quantity_precision')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('reg_no',__('lang_v1.reg_no') . ':*') !!}
                {!! Form::text('reg_no', !empty($business->reg_no) ? $business->reg_no : null, ['class' => 'form-control',
                'placeholder' => __('lang_v1.reg_no')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('popup_load_save_data', 1, $business->popup_load_save_data ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.popup_load_save_data' ) }} </label> @if(!empty($help_explanations['popup_load_auto_save_data'])) @show_tooltip($help_explanations['popup_load_auto_save_data']) @endif
                    
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('day_end_enable', 1, $business->day_end_enable ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.day_end' ) }} </label>  @if(!empty($help_explanations['day_end'])) @show_tooltip($help_explanations['day_end']) @endif
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('enable_line_discount', 1, $business->enable_line_discount ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_line_discount' ) }}  </label> @if(!empty($help_explanations['enable_line_discount'])) @show_tooltip($help_explanations['enable_line_discount']) @endif
                   
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" style="margin-top: 32px;">
            <label>
                {!! Form::checkbox('show_for_customers', 1, $business->show_for_customers, ['class' => 'input-icheck', 'id' =>
                'show_for_customers']); !!} @lang('business.show_for_customers')  </label> @if(!empty($help_explanations['need_to_show_for_the_customer'])) @show_tooltip($help_explanations['need_to_show_for_the_customer']) @endif
               
            
            </div>
        </div>
        
        <div class="col-md-4 business_categories_div @if($business->show_for_customers == 0) hide @endif">
            <div class="form-group">
                {!! Form::label('business_categories', __('business.business_categories') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-bars"></i>
                    </span>
                    {!! Form::select('business_categories[]', $business_categories, !empty($business->business_categories) ? json_decode($business->business_categories) : null, ['class' => 'form-control select2
                    business_categories', 'id' => 'business_categories', 'multiple', 'style' => 'width: 100%; margin:0px;']);
                    !!}
                </div>
            </div>
        </div>
    </div>
</div>
