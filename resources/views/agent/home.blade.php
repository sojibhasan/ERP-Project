@extends('layouts.agent')

@section('title', __('home.home'))



@section('content')



<!-- Content Header (Page header) -->

<section class="content-header">
     <h1>{{ __('home.welcome_message', ['name' => 'hey']) }}

    </h1>

</section>

<!-- Main content -->

<section class="content no-print">

	<div class="row">

		<div class="col-md-12 col-xs-12">

			<div class="btn-group pull-right" data-toggle="buttons">

				<label class="btn btn-info active">

    				<input type="radio" name="date-filters"

    				data-start="{{ date('Y-m-d') }}" 

    				data-end="{{ date('Y-m-d') }}"

    				checked> {{ __('home.today') }}

  				</label>

  				<label class="btn btn-info">

    				<input type="radio" name="date-filters"
               
    				data-start="" 

    				data-end=""

    				> {{ __('home.this_week') }}

  				</label>

  				<label class="btn btn-info">

    				<input type="radio" name="date-filters"

    				data-start="" 

    				data-end=""

    				> {{ __('home.this_month') }}

  				</label>

  				<label class="btn btn-info">

    				<input type="radio" name="date-filters" 

    				data-start="" 

    				data-end="" 

    				> {{ __('home.this_fy') }}

  				</label>

        </div>

		</div>

	</div>

	<br>

	<div class="row">

    	<div class="col-md-4 col-sm-6 col-xs-12">

	      <div class="info-box">

	        <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>



	        <div class="info-box-content">

	          <span class="info-box-text">{{ __('home.total_purchase') }}</span>

	          <span class="info-box-number total_purchases"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>

	        </div>

	        <!-- /.info-box-content -->

	      </div>

	      <!-- /.info-box -->

	    </div>

	    <!-- /.col -->

	    <div class="col-md-4 col-sm-6 col-xs-12">

	      <div class="info-box">

	        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-cart-outline"></i></span>



	        <div class="info-box-content">

	          <span class="info-box-text">{{ __('customer.total_paid') }}</span>

	          <span class="info-box-number total_paids"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>

	        </div>

	        <!-- /.info-box-content -->

	      </div>

	      <!-- /.info-box -->

	    </div>

	    <!-- /.col -->

	    <div class="col-md-4 col-sm-6 col-xs-12">

	      <div class="info-box">

	        <span class="info-box-icon bg-red">

	        	<i class="fa fa-dollar"></i>

				<i class="fa fa-exclamation"></i>

	        </span>



	        <div class="info-box-content">

	          <span class="info-box-text">{{ __('home.purchase_due') }}</span>

	          <span class="info-box-number purchase_dues"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>

	        </div>

	        <!-- /.info-box-content -->

	      </div>

	      <!-- /.info-box -->

	    </div>

	    <!-- /.col -->

  	</div>

      <br>

    <div class="row">

        <div class="col-sm-12">



        </div>

    </div>



</section>

<!-- /.content -->

@stop

@section('javascript')

	<script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>

	<script src="https://code.highcharts.com/highcharts.js"></script>

   



    <script>

        $(document).ready(function() {

            var start = $('input[name="date-filters"]:checked').data('start');

            var end = $('input[name="date-filters"]:checked').data('end');

            update_statistics(start, end);

            $(document).on('change', 'input[name="date-filters"]', function() {

                var start = $('input[name="date-filters"]:checked').data('start');

                var end = $('input[name="date-filters"]:checked').data('end');

                update_statistics(start, end);

            });



            function update_statistics(start, end) {

                var data = { start: start, end: end };

                //get purchase details

                var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';

                $('.total_purchases').html(loader);

                $('.total_paids').html(loader);

                $('.purchase_dues').html(loader);

                $.ajax({

                    method: 'get',

                    url: '/customer/home/get-totals',

                    dataType: 'json',

                    data: data,

                    success: function(data) {

                        //purchase details

                        $('.total_purchases').html(__currency_trans_from_en(data.total_purchases, true));

                        $('.purchase_dues').html(__currency_trans_from_en(data.purchase_dues, true));

                        $('.total_paids').html(__currency_trans_from_en(data.total_paids, true));

                    },

                });

            }

        });

    </script>

@endsection



