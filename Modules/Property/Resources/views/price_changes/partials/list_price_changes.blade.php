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
                {!! Form::label('project_id', __('property::lang.project'), []) !!}
                {!! Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'style' => 'width: 100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('block_id', __('property::lang.block_number')) !!}
                {!! Form::select('block_id',
                $blocks, null, ['class' => 'form-control select2', 'id' => 'block_id', 'placeholder' =>
                __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('officer_id', __('property::lang.sale_officers')) !!}
                {!! Form::select('officer_id',
                $sale_officers, null, ['class' => 'form-control select2', 'id' => 'officer_id', 'placeholder' =>
                __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sales_commission_status', __('property::lang.sales_commission_status')) !!}
                {!! Form::select('sales_commission_status',
                $sales_commission_status, null, ['class' => 'form-control select2', 'id' => 'sales_commission_status', 'placeholder' =>
                __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('commission_entered_by', __('property::lang.commission_entered_by')) !!}
                {!! Form::select('commission_entered_by',
                $users, null, ['class' => 'form-control select2', 'id' => 'commission_entered_by', 'placeholder' =>
                __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('approved_by', __('property::lang.approved_by')) !!}
                {!! Form::select('approved_by',
                $users, null, ['class' => 'form-control select2', 'id' => 'approved_by', 'placeholder' =>
                __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range', __('report.date_range') . ':') !!}
                {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('property::lang.list_price_changes')])

        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="price_changes_table">
                <thead>
                <tr>
                    <th>@lang('property::lang.sold_date')</th>
                    <th>@lang('property::lang.property_name')</th>
                    <th>@lang('property::lang.block_number')</th>
                    <th>@lang('property::lang.sale_price')</th>
                    <th>@lang('property::lang.sold_price')</th>
                    <th>@lang('property::lang.changed_amount')</th>
                    <th>@lang('property::lang.sales_officer')</th>
                    <th>@lang('property::lang.commission')</th>
                    <th>@lang('property::lang.approval')</th>
                    <th>@lang('property::lang.status')</th>
                </tr>
                </thead>
                <tfoot>

                </tfoot>
            </table>
        </div>

    @endcomponent
</section>