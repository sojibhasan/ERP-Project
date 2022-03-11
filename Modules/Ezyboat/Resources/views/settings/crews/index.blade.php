<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('crew_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('crew_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'crew_date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('employee_no', __( 'ezyboat::lang.employee_no' )) !!}
                {!! Form::select('employee_no', $employee_nos, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'employee_no']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('crew_name', __( 'ezyboat::lang.crew_name' )) !!}
                {!! Form::select('crew_name', $crew_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'crew_name']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('nic_number', __( 'ezyboat::lang.nic_number' )) !!}
                {!! Form::select('nic_number', $nic_numbers, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'nic_number']);
                !!}
            </div>
        </div>
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('ezyboat::lang.all_your_crews')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal"
      data-href="{{action('\Modules\Ezyboat\Http\Controllers\CrewController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="crew_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('ezyboat::lang.joined_date')</th>
          <th>@lang('ezyboat::lang.employee_no')</th>
          <th>@lang('ezyboat::lang.crew_name')</th>
          <th>@lang('ezyboat::lang.nic_number')</th>
          <th>@lang('ezyboat::lang.license_number')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->