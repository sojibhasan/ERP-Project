
<!-- Main content -->
<section class="content no-print">
  @component('components.filters', ['title' => __('report.filters')])
  <div class="col-md-3">
      <div class="form-group">
          {!! Form::label('agent_id', __('superadmin::lang.referral_code_agent') . ':') !!}
          {!! Form::select('agent_id', $agents, null, ['class' => 'form-control select2',
          'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
      </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      {!! Form::label('package_id', __('superadmin::lang.packages') . ':') !!}
      {!! Form::select('package_id', $packages, null, ['class' => 'form-control select2',
          'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        {!! Form::label('income_method_id', __('superadmin::lang.income_methods') . ':') !!}
        {!! Form::select('income_method_id', $income_methods, null, ['class' => 'form-control select2',
          'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
      </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('agent_dashboard_date_range', __('superadmin::lang.date_range') . ':') !!}
            {!! Form::text('agent_dashboard_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
  @endcomponent
  <div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_agents') }}</span>
          <span class="info-box-number total_agents"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
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
          <span class="info-box-text">{{ __('superadmin::lang.new_agents') }}</span>
          <span class="info-box-number new_agents"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
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
          <span class="info-box-text">{{ __('superadmin::lang.total_new_pacakges') }}</span>
          <span class="info-box-number total_new_pacakges"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <!-- <div class="clearfix visible-sm-block"></div> -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-yellow">
          <i class="ion ion-ios-paper-outline"></i>
          <i class="fa fa-exclamation"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_new_pacakges_amounts') }}</span>
          <span class="info-box-number total_new_pacakges_amounts"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-yellow">
          <i class="ion ion-ios-paper-outline"></i>
          <i class="fa fa-exclamation"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_new_subscription_amount') }}</span>
          <span class="info-box-number total_new_subscription_amount"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-yellow">
          <i class="ion ion-ios-paper-outline"></i>
          <i class="fa fa-exclamation"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_agents_payments') }}</span>
          <span class="info-box-number total_agents_payments"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-yellow">
          <i class="ion ion-ios-paper-outline"></i>
          <i class="fa fa-exclamation"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_agents_paid_amount') }}</span>
          <span class="info-box-number total_agents_paid_amount"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-yellow">
          <i class="ion ion-ios-paper-outline"></i>
          <i class="fa fa-exclamation"></i>
        </span>

        <div class="info-box-content">
          <span class="info-box-text">{{ __('superadmin::lang.total_agents_due_amount') }}</span>
          <span class="info-box-number total_agents_due_amount"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  </div>

</section>
