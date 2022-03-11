<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @component('components.filters', ['title' => __('report.filters')])
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_date_range_filter', __('report.date_range') . ':') !!}
          {!! Form::text('helper_date_range_filter', @format_date('first day of this month') . ' ~ ' .
          @format_date('last
          day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
          'form-control date_range', 'id' => 'helper_date_range_filter', 'readonly']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_employee_no', __( 'fleet::lang.employee_no' )) !!}
          {!! Form::select('helper_employee_no', $helper_employee_nos, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_employee_no']);
          !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_name', __( 'fleet::lang.helper_name' )) !!}
          {!! Form::select('helper_name', $helper_names, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_name']);
          !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('helper_nic_number', __( 'fleet::lang.nic_number' )) !!}
          {!! Form::select('helper_nic_number', $helper_nic_numbers, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'helper_nic_number']);
          !!}
        </div>
      </div>
      @endcomponent
    </div>
  </div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('fleet::lang.all_your_helpers')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal"
      data-href="{{action('\Modules\Fleet\Http\Controllers\HelperController@create')}}" data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="helper_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('fleet::lang.joined_date')</th>
          <th>@lang('fleet::lang.employee_no')</th>
          <th>@lang('fleet::lang.helper_name')</th>
          <th>@lang('fleet::lang.nic_number')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->