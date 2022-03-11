<div class="modal-dialog modal-lg no-print" role="document" style="
    width: 70%;">
  <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> 
      @lang('sale.sell_details') (<b>@lang('sale.invoice_no'):</b> {{ $sell->invoice_no }})
    </h4>
</div>
<div class="modal-body">
    <div class="row">
  <div class="col-sm-6">
        @if(!empty($sell->contact))  
          <b>{{ __('customer.customer') }}:</b>
          <br>
          {{ $sell->contact->name }}<br> 
        @endif
        {{ __('business.address') }}:<br>
        @if(!empty($sell->billing_address()))
          {{$sell->billing_address()}}
        @else
          @if(!empty($sell->contact))
            @if($sell->contact->landmark)
                {{ $sell->contact->landmark }},
            @endif
            {{ $sell->contact->city }}
            @if($sell->contact->state)
                {{ ', ' . $sell->contact->state }}
            <br>
            @endif
            @if($sell->contact->country)
                {{ $sell->contact->country }}
            <br>
            @endif
            @if($sell->contact->mobile)
                {{__('contact.mobile')}}: {{ $sell->contact->mobile }}
            @endif
            @if($sell->contact->alternate_number)
            <br>
                {{__('contact.alternate_contact_number')}}: {{ $sell->contact->alternate_number }}
            @endif
            @if($sell->contact->landline)
              <br>
                {{__('contact.landline')}}: {{ $sell->contact->landline }}
            @endif
          @endif
        @endif
      </div>
      <div class="col-sm-6">
     <b>@lang('sale.invoice_no')</b> : {{ $sell->invoice_no }} <br>
     <b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}
<!--         <b>{{ __('business.business') }}:</b> <br>
         #{{ $sell->invoice_no }}<br>
        <b>{{ __('sale.status') }}:</b> 
          @if($sell->status == 'draft' && $sell->is_quotation == 1)
            @if($sell->is_customer_order == 1)
            {{ __('lang_v1.customer_order') }}
            @else
            {{ __('lang_v1.quotation') }}
            @endif
          @else
            {{ __('sale.' . $sell->status) }}
          @endif
        <br>
        <b>{{ __('sale.payment_status') }}:</b> @if(!empty($sell->payment_status)){{ __('lang_v1.' . $sell->payment_status) }}<br>
        @endif -->
      </div>    
    </div><br>
    <div class="row">
      <div class="col-md-6">
        <b>@lang('sale.amount')</b> : {{ number_format($sell->final_total,$company->currency_precision) }} <br>
        <b>@lang('sale.payment_method')</b> : 
          @php
            $paid_in_types = ['customer_page' => 'Customer Page', 'all_sale_page' => 'All Sale Page', 'settlement' => 'Settlement'];
          @endphp
          @if ($sell->sub_type == 'cheque')
            <b>@lang('sale.cheque_number')</b> : {{ $sell->credit_sale_id }} <br>
          @else
            {{$sell->payment_lines[0]->method}} <br>
            @if($sell->payment_lines[0]->method == "cheque")
              <b>@lang('sale.cheque_number')</b> : {{ $sell->credit_sale_id }} <br>
              <b>@lang('sale.bank_name')</b> : {{ $sell->payment_lines[0]->bank_name }} <br>
              <b>@lang('sale.cheque_date')</b> : {{ $sell->payment_lines[0]->created_at->format('m/d/Y') }} <br>
            @endif
          @endif
            @if(count($sell->payment_lines) > 1)
                  </br>
                <div class="col-sm-6"><b>@lang('sale.bill_no')</b></div>
                <div class="col-sm-6"><b>@lang('sale.amount')</b></div>
              @foreach($sell->payment_lines as $payment_line)
                  <div class="col-sm-6">{{$payment_line->transaction_id}}</div>
                  <div class="col-sm-6">{{number_format($payment_line->amount,$company->currency_precision)}}</div>
              @endforeach
            @endif
      </div>
      <div class="col-md-6">
        <b>@lang('sale.payment_note')</b> : {{ $sell->payment_note }} <br>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.products') }}:</h4>
      </div>
      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          @include('sale_pos.partials.sale_line_details')
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>#</th>
              <th>{{ __('messages.date') }}</th>
              <th>{{ __('purchase.ref_no') }}</th>
              <th>{{ __('sale.amount') }}</th>
              <th>{{ __('sale.payment_mode') }}</th>
              <th>{{ __('sale.payment_note') }}</th>
            </tr>
            @php
              $total_paid = 0;
            @endphp
            @foreach($sell->payment_lines as $payment_line)
              @php
                if($payment_line->is_return == 1){
                  $total_paid -= $payment_line->amount;
                } else {
                  $total_paid += $payment_line->amount;
                }
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ @format_date($payment_line->paid_on) }}</td>
                <td>{{ $payment_line->payment_ref_no }}</td>
                <td><span class="display_currency" data-currency_symbol="true">{{ number_format($payment_line->amount,$company->currency_precision) }}</span></td>
                <td>
                  {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                  @if($payment_line->is_return == 1)
                    <br/>
                    ( {{ __('lang_v1.change_return') }} )
                  @endif
                </td>
                <td>@if($payment_line->note) 
                  {{ ucfirst($payment_line->note) }}
                  @else
                  --
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr>
              <th>{{ __('sale.total') }}: </th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true">{{number_format($sell->total_before_tax,$company->currency_precision)  }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.discount') }}:</th>
              <td><b>(-)</b></td>
              <td><div class="pull-right"><span class="display_currency" @if( $sell->discount_type == 'fixed') data-currency_symbol="true" @endif>{{ number_format($sell->discount_amount,$company->currency_precision) }}</span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></div></td>
            </tr>
            @if(in_array('types_of_service' ,$enabled_modules) && !empty($sell->packing_charge))
              <tr>
                <th>{{ __('lang_v1.packing_charge') }}:</th>
                <td><b>(+)</b></td>
                <td><div class="pull-right"><span class="display_currency" @if( $sell->packing_charge_type == 'fixed') data-currency_symbol="true" @endif>{{ number_format($sell->packing_charge,$company->currency_precision) }}</span> @if( $sell->packing_charge_type == 'percent') {{ '%'}} @endif </div></td>
              </tr>
            @endif
            @if(session('business.enable_rp') == 1 && !empty($sell->rp_redeemed) )
              <tr>
                <th>{{session('business.rp_name')}}:</th>
                <td><b>(-)</b></td>
                <td> <span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->rp_redeemed_amount,$company->currency_precision) }}</span></td>
              </tr>
            @endif
            <tr>
              <th>{{ __('sale.order_tax') }}:</th>
              <td><b>(+)</b></td>
              <td class="text-right">
                @if(!empty($order_taxes))
                  @foreach($order_taxes as $k => $v)
                    <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($v,$company->currency_precision) }}</span><br>
                  @endforeach
                @else
                {{number_format(0,$company->currency_precision)}}
                @endif
              </td>
            </tr>
            <tr>
              <th>{{ __('sale.shipping') }}: @if($sell->shipping_details)({{$sell->shipping_details}}) @endif</th>
              <td><b>(+)</b></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->shipping_charges,$company->currency_precision) }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_payable') }}: </th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->final_total,$company->currency_precision) }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_paid') }}:</th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format($total_paid,$company->currency_precision) }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_remaining') }}:</th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format(($sell->final_total - $total_paid),$company->currency_precision) }}</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <strong>{{ __( 'sale.sell_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->additional_notes)
            {{ $sell->additional_notes }}
          @else
            --
          @endif
        </p>
      </div>
      <div class="col-sm-6">
        <strong>{{ __( 'sale.staff_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->staff_note)
            {{ $sell->staff_note }}
          @else
            --
          @endif
        </p>
      </div>
    </div> 
  </div>
  <div class="modal-footer">
    <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>
