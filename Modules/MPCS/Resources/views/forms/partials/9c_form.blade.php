
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
         
             <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('form_9C_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('form_9C_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_9C_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_9C_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.from_no') . ':') !!}
                    {!! Form::text('form_9C_from_no', $F9C_sn, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
         
           
            @endcomponent
        </div>
    </div>

    <div id="9c_details_section">
        
    </div>
   
</section>
<!-- /.content -->