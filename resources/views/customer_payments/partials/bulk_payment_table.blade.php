<tr>
    <td colspan="4"></td>
    <td>@lang('lang_v1.excess_amount')</td>
    <td class="interest_selection_checkbox hide"></td>
    <td>
        <div class="row">
            <div class="col-md-1">
                {!! Form::checkbox('pay_all', 1, false, ['class' => 'input-icheck pay_all','style' => 'margin-top: 10px;transform: scale(2)']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::number('excess_amount', 0, ['class' => 'form-control', 'id' => 'excess_amount']) !!}
            </div>
        </div>
    </td>
</tr>
@if (!empty($sells) && $sells->count() > 0)
    @foreach ($sells as $sell)
        <tr>
            <td>{{ @format_date($sell->transaction_date)}}</td>
            <td>{{ $sell->invoice_no }}</td>
            <td>{{ $sell->type == 'opening_balance' ?
                'Opening Balance' : $sell->order_no}}
            </td>
            <td>{{ @num_format($sell->final_total) }}</td>
            <td>{{ @num_format($sell->final_total-$sell->total_paid) }}
                {!! Form::hidden('outstanding['.$sell->transaction_id.']', $sell->final_total-$sell->total_paid, ['id' => 'outstanding_amount_'.$sell->transaction_id, 'class' => 'form-control outstanding_'.$sell->transaction_id , 'data-id' => $sell->transaction_id ]) !!}
            </td>
            <td class="interest_selection_checkbox hide">
                <div class="row">
                    <div class="col-md-6 ">
                        {!! Form::number('interest['.$sell->transaction_id.']', null, ['id' => 'interest_amount_'.$sell->transaction_id , 'class' => 'form-control interest_column_total interest_selection_checkbox hide interest_'.$sell->transaction_id , 'data-id' => $sell->transaction_id ]) !!}
                    </div>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-md-1">
                        {!! Form::checkbox('paying['.$sell->transaction_id.']', 1, false, ['class' => 'input-icheck paying_checkbox paying_'.$sell->transaction_id, 'data-id' => $sell->transaction_id ,'style' => 'margin-top: 10px;transform: scale(2)']) !!}
                    </div>
                    <div class="col-md-4">
                        {!! Form::number('amount['.$sell->transaction_id.']', null, ['id' => 'total_amount_'.$sell->transaction_id, 'class' => 'form-control amount_column_total amount_selection_box amount_'.$sell->transaction_id , 'data-id' => $sell->transaction_id ]) !!}
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr class="text-center">
        <td colspan="6">@lang('lang_v1.no_data_available_in_table')</td>
    </tr>
    {{--<tr>--}}
    {{--    <td>31/12-95</td>--}}
    {{--    <td>YYY2255665</td>--}}
    {{--    <td>YDT256355</td>--}}
    {{--    <td>23170</td>--}}
    {{--    <td>20100--}}
    {{--        {!! Form::hidden('outstanding[2]', 3, ['class' => 'form-control outstanding_0' , 'data-id' => 1 ]) !!}--}}
    {{--    </td>--}}
    {{--    <td>--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-md-6 interest_selection_checkbox hide">--}}
    {{--                {!! Form::text('amount[2]', null, ['class' => 'form-control amount_ ' , 'data-id' =>1 ]) !!}--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </td>--}}
    {{--    <td>--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-md-1">--}}
    {{--                {!! Form::checkbox('paying[1]', 1, false, ['class' => 'input-icheck paying_checkbox paying_1', 'data-id' => 1 ,'style' => 'margin-top: 10px;margin-left: 10px;transform: scale(2)']) !!}--}}
    {{--            </div>--}}
    {{--            <div class="col-md-4">--}}
    {{--                {!! Form::text('amount[1]', null, ['class' => 'form-control amount_1' , 'data-id' => 1 ]) !!}--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </td>--}}
    {{--</tr>--}}
@endif
