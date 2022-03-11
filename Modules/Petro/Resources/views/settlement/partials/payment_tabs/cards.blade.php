<br><br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('card_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                {!! Form::select('card_type', $card_types, null, ['class' => 'form-control card_fields
                select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_number', __( 'petro::lang.card_number' ) ) !!}
                {!! Form::text('card_number', null, ['class' => 'form-control card_fields input_number
                card_number',
                'placeholder' => __(
                'petro::lang.card_number' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('card_amount', null, ['class' => 'form-control card_fields input_number
                card_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3 pull-right">
            <button type="button" class="btn btn-primary card_add pull-right"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
       
    </div>
</div>
<div class="clearfix"></div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="card_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.card_type' )</th>
                    <th>@lang('petro::lang.card_number' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $card_total = $settlement_card_payments->sum('amount');
                @endphp
                @foreach ($settlement_card_payments as $card_payment)
                    <tr>
                        <td>{{$card_payment->customer_name}}</td>
                        <td>{{$card_payment->card_type}}</td>
                        <td>{{$card_payment->card_number}}</td>
                        <td class="card_amount">{{number_format($card_payment->amount, $currency_precision)}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="/petro/settlement/payment/delete-card-payment/{{$card_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="card_total">
                       {{number_format($card_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$card_total}}" name="card_total" id="card_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $(document).ready(function(){
        $("#card_customer_id").val($("#card_customer_id option:eq(0)").val()).trigger('change');
        
    });
</script>