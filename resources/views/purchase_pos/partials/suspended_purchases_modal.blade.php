<!-- Edit Order tax Modal -->
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang('lang_v1.suspended_purchases')</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				@php
					$c = 0;
				@endphp
				@forelse($purchases as $purchase)
					@if($purchase->is_suspend)
						<div class="col-xs-6 col-sm-3 sale-{{$purchase->id}}">
							<div class="small-box bg-yellow">
					            <div class="inner text-center">
						            @if(!empty($purchase->additional_notes))
						            	<p><i class="fa fa-edit"></i> {{$purchase->additional_notes}}</p>
						            @endif
					              <p>{{$purchase->invoice_no}}<br>
					              {{@format_date($purchase->transaction_date)}}<br>
					              <strong><i class="fa fa-user"></i> {{$purchase->name}}</strong></p>
					              <p><i class="fa fa-cubes"></i>@lang('lang_v1.total_items'): {{count($purchase->purchase_lines)}}<br>
					              <i class="fa fa-money"></i> @lang('sale.total'): <span class="display_currency" data-currency_symbol=true>{{$purchase->final_total}}</span>
					              </p>
					            </div>
					            <a href="{{action('PurchasePosController@edit', $purchase->id)}}" class="small-box-footer">
					              @lang('lang_v1.edit_purchase') <i class="fa fa-arrow-circle-right"></i>
					            </a>
							 </div>
							 <a href="{{action('PurchaseController@destroy', [$purchase->id])}}" class="delete-sale-suspend btn" style=" background: brown; width: 100%; color: white;">Cancel</a>
				         </div>
				        @php
				         	$c++;
				        @endphp
					@endif

					@if($c%4==0)
						<div class="clearfix"></div>
					@endif
				@empty
					<p class="text-center">@lang('purchase.no_records_found')</p>
				@endforelse
			</div>
		</div>
		<div class="modal-footer">
		    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
