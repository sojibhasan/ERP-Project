@php
$transaction_types = [];

$transaction_types['opening_balance'] = __('lang_v1.opening_balance');
@endphp
<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
            </div>
        </div>
        <div class="col-md-9 text-right">
            <button data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@getLedger')}}?pump_operator_id={{$pump_operator->id}}&action=print"
            class="btn btn-default btn-xs" id="print_btn"><i class="fa fa-print"></i></button>
            <button
                data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@getLedger')}}?pump_operator_id={{$pump_operator->id}}&action=pdf"
                class="btn btn-default btn-xs" id="print_ledger_pdf"><i class="fa fa-file-pdf-o "></i></button>

            {{-- <button type="button" class="btn btn-default btn-xs" id="send_ledger"><i
                    class="fa fa-envelope"></i></button> --}}
        </div>
        @endcomponent
    </div>
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
        <div id="contact_ledger_div"></div>
        @endcomponent
    </div>
</div>