<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.credit_status')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        {!! Form::open(['url' => action('ReportController@getManagementReport'), 'id' => 'credit_filter_form', 'method' => 'get']) !!}
        {{-- <form action=""> --}}
        <div class="col-md-6 col-xs-6">
            <div class="col-md-6">
                {!! Form::label('credit_status_business_location', __('business.business_locations'), []) !!}
                {!! Form::select('credit_status_business_location', $business_locations, !empty(request()->credit_status_business_location) ? request()->credit_status_business_location : null, ['class'=> 'form-control credit_filter_change
                select2',
                'placeholder' => __('lang_v1.all'), 'id' => 'credit_status_business_location', 'style' => 'width: 100%;']) !!}
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('credit_status_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', !empty(request()->date_range) ? request()->date_range :  @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control credit_filter_change', 'id' => 'credit_status_date_range', 'readonly']); !!}
                </div>
            </div>

            <input type="hidden" name="credit_start_date" id="credit_start_date">
            <input type="hidden" name="credit_end_date" id="credit_end_date">

        </div>
      
        <div class="col-md-6 col-xs-6">
            <div class="btn-group pull-right" data-toggle="buttons">
                <label class="btn btn-info @if(request()->period == 1 || empty(request()->period ) ) active @endif ">
                    <input type="radio" class="credit_filter_change" name="period" value="1" @if(request()->period == 1 || empty(request()->period )) checked @endif > {{ __('report.daily') }}
                </label>
                <label class="btn btn-info @if(request()->period == 7 ) active @endif ">
                    <input type="radio" class="credit_filter_change" name="period" value="7" @if(request()->period == 7 ) checked @endif > {{ __('report.weekly') }}
                </label>
                <label class="btn btn-info @if(request()->period == 30 ) active @endif ">
                    <input type="radio" class="credit_filter_change" name="period" value="30" @if(request()->period == 30 ) checked @endif > {{ __('report.monthly') }}
                </label>
                <label class="btn btn-info @if(request()->period == 120 ) active @endif ">
                    <input type="radio" class="credit_filter_change" name="period" value="120" @if(request()->period == 120 ) checked @endif > {{ __('report.quarterly') }}
                </label>
                <label class="btn btn-info @if(request()->period == 365 ) active @endif ">
                    <input type="radio" class="credit_filter_change" name="period" value="365" @if(request()->period == 365 ) checked @endif > {{ __('report.annually') }}
                </label>
            </div>
        </div>
    </div>
    <br>
    <input type="hidden" name="tab" value="credit_status">
    {!! Form::close() !!}
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('report.credit_issued') }}</span>
                    <span class="info-box-number total_credit_issued"><i
                            class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('report.credit_received') }}</span>
                    <span class="info-box-number total_credit_paid"><i
                            class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow">
                    <i class="fa fa-dollar"></i>
                    <i class="fa fa-exclamation"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('report.credit_due') }}</span>
                    <span class="info-box-number total_credit_due"><i
                            class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
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
            @component('components.widget', ['class' => 'box-primary', 'title' => __('report.credit_status')])
            {!! $sells_chart_1->container() !!}
            @endcomponent
        </div>
        
    </div>
</section>

</div>