<br><br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('shortage_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('shortage_amount', null, ['class' => 'form-control shortage_fields input_number
                shortage_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary shortage_add"
                style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="shortage_table">
            <thead>
                <tr>
                    <th></th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                $shortage_total = $settlement_shortage_payments->sum('amount');
                @endphp
                @foreach ($settlement_shortage_payments as $shortage_payment)
                <tr>
                    <td></td>
                    <td class="shortage_amount">{{number_format($shortage_payment->amount, $currency_precision)}}</td>
                    <td><button type="button" class="btn btn-xs btn-danger delete_shortage_payment"
                            data-href="/petro/settlement/payment/delete-shortage-payment/{{$shortage_payment->id}}"><i
                                class="fa fa-times"></i></button></td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="shortage_total">
                        {{number_format($shortage_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$shortage_total}}" name="shortage_total" id="shortage_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $(document).ready(function(){
        $("#shortage_customer_id").val($("#shortage_customer_id option:eq(0)").val()).trigger('change');
        
    });
</script>