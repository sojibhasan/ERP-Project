<br><br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('excess_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('excess_amount', null, ['class' => 'form-control excess_fields input_number
                excess_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary excess_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="excess_table">
            <thead>
                <tr>
                    <th></th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $excess_total = $settlement_excess_payments->sum('amount');
                @endphp
                @foreach ($settlement_excess_payments as $excess_payment)
                    <tr>
                        <td></td>
                        <td class="excess_amount">{{number_format($excess_payment->amount, $currency_precision)}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_excess_payment" data-href="/petro/settlement/payment/delete-excess-payment/{{$excess_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="excess_total">
                       {{number_format($excess_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$excess_total}}" name="excess_total" id="excess_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $(document).ready(function(){
        $("#excess_customer_id").val($("#excess_customer_id option:eq(0)").val()).trigger('change');
        
    });
</script>