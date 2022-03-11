<br><br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cheque_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('cheque_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('bank_name', __( 'petro::lang.bank_name' ) ) !!}
                {!! Form::text('bank_name', null, ['class' => 'form-control cheque_fields
                bank_name',
                'placeholder' => __(
                'petro::lang.bank_name' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cheque_date', __( 'petro::lang.cheque_date' ) ) !!}
                {!! Form::text('cheque_date', null, ['class' => 'form-control cheque_fields
                cheque_date',
                'placeholder' => __(
                'petro::lang.cheque_date' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cheque_number', __( 'petro::lang.cheque_number' ) ) !!}
                {!! Form::text('cheque_number', null, ['class' => 'form-control cheque_fields input_number
                cheque_number',
                'placeholder' => __(
                'petro::lang.cheque_number' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cheque_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('cheque_amount', null, ['class' => 'form-control cheque_fields input_number
                cheque_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary cheque_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="cheque_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.bank_name' )</th>
                    <th>@lang('petro::lang.cheque_number' )</th>
                    <th>@lang('petro::lang.cheque_date' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cheque_total = $settlement_cheque_payments->sum('amount');
                @endphp
                @foreach ($settlement_cheque_payments as $cheque_payment)
                    <tr>
                        <td>{{$cheque_payment->customer_name}}</td>
                        <td>{{$cheque_payment->bank_name}}</td>
                        <td>{{$cheque_payment->cheque_number}}</td>
                        <td>{{@format_date($cheque_payment->cheque_date)}}</td>
                        <td class="cheque_amount">{{number_format($cheque_payment->amount, $currency_precision)}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cheque_payment" data-href="/petro/settlement/payment/delete-cheque-payment/{{$cheque_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="cheque_total">
                       {{number_format($cheque_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$cheque_total}}" name="cheque_total" id="cheque_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $(document).ready(function(){
        $("#cheque_customer_id").val($("#cheque_customer_id option:eq(0)").val()).trigger('change');
        $('#cheque_date').datepicker("setDate", new Date());
    });
</script>