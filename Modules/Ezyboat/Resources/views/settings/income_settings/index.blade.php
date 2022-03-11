<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @component('components.filters', ['title' => __('report.filters')])
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('income_setting_date_range_filter', __('report.date_range') . ':') !!}
          {!! Form::text('income_setting_date_range_filter', @format_date('first day of this month') . ' ~ ' .
          @format_date('last
          day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
          'form-control date_range', 'id' => 'income_setting_date_range_filter', 'readonly']); !!}
        </div>
      </div>
   
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('income_name', __( 'ezyboat::lang.income_name' )) !!}
          {!! Form::select('income_name', $income_names, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'ezyboat::lang.please_select' ), 'id' => 'income_name']);
          !!}
        </div>
      </div>
    
      @endcomponent
    </div>
  </div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('ezyboat::lang.all_your_income_settings')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal"
      data-href="{{action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@create')}}" data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="income_setting_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('ezyboat::lang.income_name')</th>
          <th>@lang('ezyboat::lang.owner_income')</th>
          <th>@lang('ezyboat::lang.crew_income')</th>
          <th>@lang('ezyboat::lang.deduct_expense_for_income')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->