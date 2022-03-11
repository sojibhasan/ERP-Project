<br><br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('cash_customer_id', $customers, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('cash_amount', null, ['class' => 'form-control cash_fields input_number
                cash_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary cash_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="cash_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cash_total = $settlement_cash_payments->sum('amount');
                @endphp
                @foreach ($settlement_cash_payments as $cash_payment)
                    <tr>
                        <td>{{$cash_payment->customer_name}}</td>
                        <td class="cash_amount">{{number_format($cash_payment->amount, $currency_precision)}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-payment/{{$cash_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
                @if (!empty($total_daily_collection))
                    <tr>
                    <td class="text-red">@lang('petro::lang.daily_collections')</td>
                    <td class="text-red">{{number_format($total_daily_collection, $currency_precision)}}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="cash_total">
                    {{number_format($cash_total+$total_daily_collection, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$cash_total}}" name="cash_total" id="cash_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $('#cash_customer_id').select2();
    $(document).ready(function(){
        $("#cash_customer_id").val($("#cash_customer_id option:eq(0)").val()).trigger('change');
        
    });
</script>