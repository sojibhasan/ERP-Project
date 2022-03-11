<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('goldsmith_id', __('ran::lang.goldsmith') . ':') !!}
                    {!! Form::select('goldsmith_id', $goldsmiths, null, ['class' => 'form-control select2 goldsmith_id',
                    'style' =>
                    'width:100%', 'id' => 'filter_goldsmith_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('product.category') . ':') !!}
                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2 category_id',
                    'style' =>
                    'width:100%', 'id' => 'filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    {!! Form::select('sub_category_id', [], null, ['class' => 'form-control select2
                    sub_category_id', 'style' =>
                    'width:100%', 'id' => 'filter_sub_category_id', 'placeholder' => __('lang_v1.all')]);
                    !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('details_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('details_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control daily_report_change', 'id' => 'details_date_range', 'readonly']); !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('ran::lang.all_your_list_wastage')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="wastage_details_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('ran::lang.date_and_time')</th>
                    <th>@lang('ran::lang.wastage_form_no')</th>
                    <th>@lang('ran::lang.goldsmith')</th>
                    <th>@lang('ran::lang.category')</th>
                    <th>@lang('ran::lang.sub_category')</th>
                    <th>@lang('ran::lang.wastage_per_8_g')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->