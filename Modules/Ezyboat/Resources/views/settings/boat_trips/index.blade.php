<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('trip_names', __( 'ezyboat::lang.trip_name' )) !!}
                {!! Form::select('trip_names', $trip_names, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'trip_names']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('starting_locations', __( 'ezyboat::lang.starting_location' )) !!}
                {!! Form::select('starting_locations', $starting_locations, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'starting_locations']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('final_locations', __( 'ezyboat::lang.final_location' )) !!}
                {!! Form::select('final_locations', $final_locations, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'final_locations']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('users', __( 'ezyboat::lang.user_added' )) !!}
                {!! Form::select('users', $users, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'ezyboat::lang.please_select' ), 'id' => 'users']);
                !!}
            </div>
        </div>
       
      
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('ezyboat::lang.all_your_routes')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal"
      data-href="{{action('\Modules\Ezyboat\Http\Controllers\BoatTripController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="boat_trips_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('ezyboat::lang.date')</th>
          <th>@lang('ezyboat::lang.trip_name')</th>
          <th>@lang('ezyboat::lang.starting_location')</th>
          <th>@lang('ezyboat::lang.final_location')</th>
          <th>@lang('ezyboat::lang.user_added')</th>

        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->