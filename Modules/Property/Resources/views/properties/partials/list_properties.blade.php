<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('supplier_id',  'Purchase No' . ':') !!}
            {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('status',  __('property::lang.status') . ':') !!}
            {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_id',  __('Project Name') . ':') !!}
            {!! Form::select('property_id', $properties , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    @endcomponent
    
    @component('components.widget', ['class' => 'box-primary', 'title' => __('property::lang.list_all_properties')])
    
    <div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view" id="property_table">
        <thead>
            <tr>
                <th class="notexport">@lang('messages.action')</th>
                <th>@lang('messages.date')</th>
                <th>@lang('purchase.location')</th>
                <th>@lang('purchase.purchase_no')</th>
                <th>@lang('property::lang.status')</th>
                <th>@lang('property::lang.property_name')</th>
                <th>@lang('property::lang.property_extent')</th>
                <th>@lang('property::lang.unit')</th>
                <th>@lang('property::lang.no_of_blocks')</th>
                <th>@lang('lang_v1.added_by')</th>
            </tr>
        </thead>
        <tfoot>
          
        </tfoot>
    </table>
    </div>
    
    @endcomponent
    </section>