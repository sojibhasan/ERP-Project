@extends($layout)

@section('title', __('superadmin::lang.subscription'))

@section('content')

<!-- Main content -->
<section class="content">

	@include('superadmin::layouts.partials.currency')
@php
	$currency_symbol = App\Currency::where('id', $package->currency_id)->first();
@endphp
	<div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">@lang('superadmin::lang.pay_and_subscribe')</h3>
        </div>

        <div class="box-body">
    		<div class="col-md-8">
        		<h3>
                    @lang('patient.no_of_family_members'): {{$no_of_family_members}}

        			<small><span class="" >{{!empty($currency_symbol)? $currency_symbol->symbol : '' }} {{$amount_to_pay}}</span></small>

        		</h3>

				<ul class="list-group">
					@foreach($gateways as $k => $v)
						<div class="list-group-item">
							<b>@lang('superadmin::lang.pay_via', ['method' => $v])</b>
							
							<div class="row" id="paymentdiv_{{$k}}">
                                @if($k == 'offline' || $k == 'payhere')
                                @php 
									$view = 'patient.pay.pay_'.$k;
								@endphp
                                @includeIf($view)
                                @endif
							</div>
						</div>
					@endforeach
				</ul>
			</div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
		@if (!empty($register_success))
			@if($register_success['success'] == 1)
				$('#register_success_modal').modal('show');
			@endif
		@endif

	});
</script>
@endsection