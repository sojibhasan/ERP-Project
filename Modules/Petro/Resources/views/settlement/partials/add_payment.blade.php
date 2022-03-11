@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="modal-dialog" role="document" style="width: 75%;">
   <div class="modal-content">

      {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\SettlementController@store'), 'method' => 'post',
      'id' =>
      'settlement_form' ]) !!}

      <div class="modal-header">
         {{-- 
            @ModifiedBy Afes oktavianus
            @DateBy 31-05-2021
            @Task  3350
         --}}

         <h4 class="modal-title pull-left" style="padding-right: 25px">@lang( 'petro::lang.add_payment' )</h4>
         <h4 class="modal-title pull-left">@lang( 'petro::lang.settlement_no' ): {{$settlement->settlement_no}}</h4>
         <h4 class="modal-title pull-right">@lang( 'petro::lang.date' ): {{$settlement->transaction_date}}</h4>
      </div>

      <div class="modal-body">
         <div class="col-md-12">
            <div class="row">
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.pump_operator')</b> <br>
                  {{$pump_operator->name}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.current_short')</b> <br>
                  {{-- 
                     /**
                     * @ModifiedBy Afes Oktavianus
                     * @Date 02-06-2021
                     * @Task 127004
                     */
                  --}}
                  {{@num_format($total_shortage)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.current_excess')</b> <br>
                  {{-- 
                     /**
                     * @ModifiedBy Afes Oktavianus
                     * @Date 02-06-2021
                     * @Task 127004
                     */
                  --}}
                  {{@num_format($total_excess)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.daily_collections')</b> <br>
                  {{@num_format($total_daily_collection)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.daily_vouchers')</b> <br>
                  {{@num_format(0)}}
               </div>
               <div class="col-md-2 text-center">
                  <b>@lang('petro::lang.commision_ammount')</b> <br>
                  {{@num_format($pump_operator->total_commision)}}
               </div>
            </div>
            <br><br>
            <div class="row">
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.total_amount'): </b>
                  <span class="total_amount">{{@num_format($total_amount)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.total_paid'): </b>
                  <span class="total_paid">{{@num_format($total_paid)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
                  <b>@lang('petro::lang.balance'): </b>
                  <span class="total_balance">{{@num_format($total_balance)}}</span>
               </div>
               <div class="col-md-3 text-center text-red">
               </div>
               <div class="col-md-3 text-center text-red">
                  <button type="button" id="settlement_save_btn" style="margin-left: 45px;"
                     class="btn btn-primary pull-left @if(!empty($total_balance) && $total_balance > 0) hide @endif">@lang('messages.save')</button>
                     <button data-href="{{action('\Modules\Petro\Http\Controllers\AddPaymentController@preview', [$settlement->id])}}" class="btn-modal btn btn-success pull-right" id="payment_review_btn" data-container=".preview_settlement"> @lang("petro::lang.preview")</button>
               </div>
              
            </div>
         </div>
         <input type="hidden" name="settlement_id" value="{{$settlement->settlementt_no}}">
         <input type="hidden" name="total_balance" id="total_balance"
            value="{{!empty($total_balance)? $total_balance : 0 }}">
         <input type="hidden" name="total_amount" id="total_amount"
            value="{{!empty($total_amount)? $total_amount : 0 }}">
         <input type="hidden" name="total_paid" id="total_paid" value="{{!empty($total_paid)? $total_paid : 0 }}">
         <br><br>
         <div class="clearfix"></div>
         <div style="margin-top: 20px;">
            @include('petro::settlement.partials.payment_tabs')
         </div>

         <div class="clearfix"></div>
         {!! Form::close() !!}
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('petro::lang.back')</button>
         </div>
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->