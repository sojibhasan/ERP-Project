@php
    $transaction_types = [];
    if(in_array($contact->type, ['both', 'supplier'])){
        $transaction_types['purchase'] = __('lang_v1.purchase');
        $transaction_types['purchase_return'] = __('lang_v1.purchase_return');
    }

    if(in_array($contact->type, ['both', 'customer'])){
        $transaction_types['sell'] = __('sale.sale');
        $transaction_types['sell_return'] = __('lang_v1.sell_return');
    }

    $transaction_types['opening_balance'] = __('lang_v1.opening_balance');
@endphp
<div class="row">
    <div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
            </div>
        </div>
        {{-- <div class="col-md-9 text-right">
            <button data-href="{{action('ContactController@getLedger')}}?contact_id={{$contact->id}}&action=print" class="btn btn-default btn-xs" id="print_btn"><i class="fa fa-print"></i></button>
            <button data-href="{{action('ContactController@getLedger')}}?contact_id={{$contact->id}}&action=pdf" class="btn btn-default btn-xs" id="print_ledger_pdf"><i class="fa fa-file-pdf-o "></i></button>

            <button type="button" class="btn btn-default btn-xs" id="send_ledger"><i class="fa fa-envelope"></i></button>
        </div> --}}
        @endcomponent
    </div>
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
        <div id="contact_ledger_div"></div>
        @endcomponent
    </div>
</div>
@if( in_array($contact->type, ['customer', 'both']) && session('business.enable_rp'))
    @component('components.widget', ['class' => 'box-primary', 'title' => session('business.rp_name')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" 
            id="rp_log_table">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('lang_v1.earned')</th>
                        <th>@lang('lang_v1.redeemed')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
@endif