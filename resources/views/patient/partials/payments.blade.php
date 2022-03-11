@php
    $bus_id = request()->session()->get('business.id');
    $package = \Modules\Superadmin\Entities\Subscription::active_subscription($bus_id);
@endphp
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'patient.all_your_payments' )])
    <div class="row">
		<div class="col-md-3 col-xs-12" style="padding-right:0px;">
            <div class="pull-left">
                <select name="filter_type" id="filter_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="1">Hospital</option>
                    <option value="2">Pharmacy</option>
                    <option value="3">Laboratory</option>
                </select>
            </div>
        </div>
		<div class="col-md-2 col-xs-12"  style="padding:0px;">
            <h4>Total: <span id="total_payments">25000</span></h4>
        </div>
		<div class="col-md-7 col-xs-12">
			<div class="btn-group pull-right" data-toggle="buttons">
				<label class="btn btn-info active">
    				<input type="radio" name="date-filter"
    				data-start="{{ date('Y-m-d') }}" 
    				data-end="{{ date('Y-m-d') }}"
    				checked> {{ __('home.today') }}
  				</label>
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_week']['start']}}" 
    				data-end="{{ $date_filters['this_week']['end']}}"
    				> {{ __('home.this_week') }}
  				</label>
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_month']['start']}}" 
    				data-end="{{ $date_filters['this_month']['end']}}"
    				> {{ __('home.this_month') }}
  				</label>
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter" 
    				data-start="{{ $date_filters['this_fy']['start']}}" 
    				data-end="{{ $date_filters['this_fy']['end']}}" 
    				> {{ __('patient.this_year') }}
  				</label>
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter" 
    				data-start="" 
    				data-end="" 
    				> {{ __('patient.total') }}
  				</label>
            </div>
		</div>
    </div>
    <div class="row" style="padding:10px;"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="payments_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'patient.date' )</th>
                            <th>@lang( 'patient.institution_name' )</th>
                            <th>@lang( 'patient.amount' )</th>
                            <th>@lang( 'patient.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
    @endcomponent

 

</section>
<!-- /.content -->

