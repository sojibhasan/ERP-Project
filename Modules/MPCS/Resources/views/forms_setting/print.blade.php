@php
$form_data = $form_values->data;
@endphp
@if($form_values->form_name == '9C')
@php
$cat_data = $form_data['cat_data'];
@endphp
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F9C_setting_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
        {!! Form::text('F9C_setting_tdate', $form_data['F9C_tdate'], ['class' => 'form-control',
        'id' => 'F9C_setting_tdate',
        'readonly', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
    </div>
</div>
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F9C_setting_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
        {!! Form::text('F9C_setting_sn',$form_data['F9C_sn'], ['class' =>
        'form-control', 'id' => 'F9C_setting_sn',
        'readonly', 'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
    </div>
</div>
<div class="clearfix"></div>
<br>
<div class="table-responsive">
    <table class="table table-bordered 9c_table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"></th>
                @foreach ($cat_data as $sub_cat)
                @php
                $cat_name = \App\Category::findOrFail($sub_cat['sub_category_id']);
                @endphp
                <th class="text-center" colspan="2" style="width: 100px;">{{$cat_name->name}}</th>
                @endforeach
            </tr>
            <br>
            <tr>
                <th></th>
                @foreach ($cat_data as $sub_cat)
                <th class="text-center" style="padding: 30px;">@lang('mpcs::lang.qty')</th>
                <th class="text-center" style="padding: 30px;">@lang('mpcs::lang.amount')</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr style="background: gainsboro;">
                <td class="text-red"><b>@lang('mpcs::lang.total_pervious_page')</b></td>
                @foreach ($cat_data as $sub_cat)
                <td>
                    <input type="text" name="" readonly value="{{!empty($sub_cat['qty']) ? $sub_cat['qty'] : 0.00}}"
                        class="form-control" style="width: 100%;">
                </td>
                <td>
                    <input type="text" name="" readonly
                        value="{{!empty($sub_cat['amount']) ? $sub_cat['amount'] : 0.00}}" class="form-control"
                        style="width: 100%;">
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
                    {!! Form::checkbox('F9C_first_day_after_stock_taking', 1,
                    !empty($form_data['F9C_first_day_after_stock_taking']) ?
                    $form_data['F9C_first_day_after_stock_taking'] : 0, ['class' =>
                    'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                    @lang('mpcs::lang.first_day_after_stock_taking')
                </label>
            </div>
            <div class="checkbox">
                <label style="float:left;">
                    {!! Form::checkbox('F9C_first_day_of_next_month', 1,
                    !empty($form_data['F9C_first_day_of_next_month']) ?
                    $form_data['F9C_first_day_of_next_month'] : 0, ['class' => 'input-icheck',
                    'id' => 'first_day_of_next_month']); !!}
                    @lang('mpcs::lang.first_day_of_next_month')
                </label> &Tab;
                {!! Form::select('F9C_first_day_of_next_month_selected', $months,
                !empty($form_data['F9C_first_day_of_next_month_selected']) ?
                $form_data['F9C_first_day_of_next_month_selected'] : null, ['class' =>
                'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right:
                10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
            </div>
            <div class="clearfix"></div>
            @lang('mpcs::lang.balance_zero_example')
        </div>
    </div>
</div>
@endif


{{-- F16A Form --}}
@if($form_values->form_name == 'F16A')
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F16A_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
        {!! Form::text('F16A_form_tdate', !empty($form_data['F16A_form_tdate']) ? date('m/d/Y',
        strtotime($form_data['F16A_form_tdate'])) : null, ['class' => 'form-control', 'id' =>
        'F16A_settings_tdate',
        'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
    </div>
</div>
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F16A_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
        {!! Form::text('F16A_form_sn', !empty($form_data['F16A_form_sn']) ? $form_data['F16A_form_sn'] :
        null, ['class' => 'form-control', 'id' => 'F16A_form_sn',
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
                <input type="text" name="F16A_total_pp"
                    value="@if(!empty($form_data['F16A_total_pp'])) {{$form_data['F16A_total_pp']}} @endif"
                    class="form-control" style="width: 60%;  margin-left: 25%;">
            </td>
            <td>
                <input type="text" name="F16A_total_sp"
                    value="@if(!empty($form_data['F16A_total_sp'])) {{$form_data['F16A_total_sp']}} @endif"
                    class="form-control" style="width: 60%;  margin-left: 25%;">
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
                    {!! Form::checkbox('F16A_first_day_after_stock_taking', 1,
                    !empty($form_data['F16A_first_day_after_stock_taking']) ?
                    $form_data['F16A_first_day_after_stock_taking'] : false, ['class' => 'input-icheck',
                    'id' => 'first_day_after_stock_taking']); !!}
                    @lang('mpcs::lang.first_day_after_stock_taking')
                </label>
            </div>
            <div class="checkbox">
                <label style="float:left;">
                    {!! Form::checkbox('F16A_first_day_of_next_month', 1,
                    !empty($form_data['F16A_first_day_of_next_month']) ?
                    $form_data['F16A_first_day_of_next_month'] : false, ['class' => 'input-icheck', 'id' =>
                    'first_day_of_next_month']); !!}
                    @lang('mpcs::lang.first_day_of_next_month')
                </label> &Tab;
                {!! Form::select('F16A_first_day_of_next_month_selected', $months,
                !empty($form_data['F16A_first_day_of_next_month_selected']) ?
                $form_data['F16A_first_day_of_next_month_selected'] : null, ['class' => 'form-control',
                'style' => 'width: 100px; float: left; margin-left: 10px; margin-right: 10px;',
                'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
            </div>
            <div class="clearfix"></div>
            @lang('mpcs::lang.balance_zero_example')
        </div>
    </div>
</div>
@endif


@if($form_values->form_name == 'F21C')

<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F21C_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
        {!! Form::text('F21C_form_sn', !empty($form_data)? $form_data['F21C_form_sn'] : null, ['class' =>
        'form-control', 'id' => 'F21C_form_sn',
        'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
    </div>
</div>
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F21C_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
        {!! Form::text('F21C_form_tdate', !empty($form_data)? $form_data['F21C_form_tdate'] : null, ['class' =>
        'form-control', 'id' => 'F21C_setting_tdate',
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
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            @endforeach
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
        <tr>
            <td><b>@lang('mpcs::lang.opening_stock')</b></td>
            @foreach ($merged_sub_categories as $merged)
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            @endforeach
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
        </tr>
        <tr>
            <td><b>@lang('mpcs::lang.price_increment_pre_date')</b></td>
            @foreach ($merged_sub_categories as $merged)
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            @endforeach
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
        </tr>
        <tr>
            <td colspan="11" class="text-red"><b>@lang('mpcs::lang.issues')</b></td>
        </tr>
        <tr>
            <td><b>@lang('mpcs::lang.previous_day')</b></td>
            @foreach ($merged_sub_categories as $merged)
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            @endforeach
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
        </tr>
        <tr>
            <td><b>@lang('mpcs::lang.price_reduction_pre_date')</b></td>
            @foreach ($merged_sub_categories as $merged)
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            @endforeach
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
            </td>
            <td>
                <input type="text" name="" class="form-control" style="width: 80%%;">
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
                    {!! Form::checkbox('F21C_first_day_after_stock_taking', 1, !empty($form_data)?
                    $form_data['F21C_first_day_after_stock_taking'] : false, ['class' => 'input-icheck', 'id' =>
                    'first_day_after_stock_taking']); !!}
                    @lang('mpcs::lang.first_day_after_stock_taking')
                </label>
            </div>
            <div class="checkbox">
                <label style="float:left;">
                    {!! Form::checkbox('F21C_first_day_of_next_month', 1, !empty($form_data)?
                    $form_data['F21C_first_day_of_next_month'] : false, ['class' => 'input-icheck', 'id' =>
                    'first_day_of_next_month']); !!}
                    @lang('mpcs::lang.first_day_of_next_month')
                </label> &Tab;
                {!! Form::select('F21C_first_day_of_next_month_selected', $months, !empty($form_data)?
                $form_data['F21C_first_day_of_next_month_selected'] : null, ['class' => 'form-control', 'style' =>
                'width: 100px; float: left; margin-left: 10px; margin-right: 10px;', 'placeholder' =>
                __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
            </div>
            <div class="clearfix"></div>
            @lang('mpcs::lang.balance_zero_example')
        </div>
    </div>
</div>
@endif

@if($form_values->form_name = "F159ABC")
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F159ABC_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
        {!! Form::text('F159ABC_form_tdate', !empty($form_data) ? $form_data['F159ABC_form_tdate'] :null, ['class' =>
        'form-control', 'id' => 'F159ABC_setting_tdate',
        'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
    </div>
</div>
<div class="col-md-4">
    <div class="form-group">
        {!! Form::label('F159ABC_form_sn', __( 'mpcs::lang.form_starting_number' ) . ':*') !!}
        {!! Form::text('F159ABC_form_sn', !empty($form_data) ? $form_data['F159ABC_form_sn'] :null, ['class' =>
        'form-control', 'id' => 'F159ABC_setting_sn',
        'required', 'placeholder' => __( 'mpcs::lang.form_starting_number' ) ]); !!}
    </div>
</div>
<div class="clearfix"></div>
<br>
<div class="col-md-12 159abc_form_tables">
    <div class="col-md-6">
        <div class="col-md-6">
            <label for="">@lang('mpcs::lang.purchase_side')</label>
            <table class="table table-responsive table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('mpcs::lang.description')</th>
                        <th class="text-center">@lang('mpcs::lang.previous_day')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @lang('mpcs::lang.opening_balance')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.purchase')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.from_own')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.exchange_return')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.price_increment')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.return')
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <label for="">@lang('mpcs::lang.sale_side')</label>
            <table class="table table-responsive table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('mpcs::lang.description')</th>
                        <th class="text-center">@lang('mpcs::lang.previous_day')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @lang('mpcs::lang.sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.others')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.price_reductions')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.sales_return')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.issue_on_society')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.credit_sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.final_stock')
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="col-md-6">
            <label for="">@lang('mpcs::lang.income_side')</label>
            <table class="table table-responsive table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('mpcs::lang.description')</th>
                        <th class="text-center">@lang('mpcs::lang.previous_day')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @lang('mpcs::lang.cash_sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.cheques_sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.credit_card_sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.other_sales')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.exchange_return')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.purchase_increment')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.return')
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <label for="">@lang('mpcs::lang.deposit_side')</label>
            <table class="table table-responsive table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('mpcs::lang.description')</th>
                        <th class="text-center">@lang('mpcs::lang.previous_day')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @lang('mpcs::lang.cash_deposited')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.cheques_deposited')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.credit_cards')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.others')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.cash_in_hand')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.purchase_increment')
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('mpcs::lang.return')
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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
                    {!! Form::checkbox('F159ABC_first_day_after_stock_taking', 1, !empty($form_data) ?
                    $form_data['F159ABC_first_day_after_stock_taking'] : false , ['class' =>
                    'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                    @lang('mpcs::lang.first_day_after_stock_taking')
                </label>
            </div>
            <div class="checkbox">
                <label style="float:left;">
                    {!! Form::checkbox('F159ABC_first_day_of_next_month', 1, !empty($form_data) ?
                    $form_data['F159ABC_first_day_of_next_month'] : false , ['class' => 'input-icheck',
                    'id' => 'first_day_of_next_month']); !!}
                    @lang('mpcs::lang.first_day_of_next_month')
                </label> &Tab;
                {!! Form::select('F159ABC_first_day_of_next_month_selected', $months, !empty($form_data) ?
                $form_data['F159ABC_first_day_of_next_month_selected'] : null, ['class' =>
                'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right:
                10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('mpcs::lang.month_end')
            </div>
            <div class="clearfix"></div>
            @lang('mpcs::lang.balance_zero_example')
        </div>
    </div>
</div>
@endif

<script>
    $('.modal-body :input').attr('disabled', true);
</script>