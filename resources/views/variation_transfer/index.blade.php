<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_location_from', __('purchase.business_location') . ':') !!}
                    {!! Form::select('filter_location_from', $business_locations, null, ['class' =>
                    'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_location_to', __('purchase.business_location') . ':') !!}
                    {!! Form::select('filter_location_to', $business_locations, null, ['class' =>
                    'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_from_store', __('lang_v1.from_store'). ':', []) !!}
                    {!! Form::select('filter_from_store', [], null, ['class' => 'form-control select2',
                    'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_to_store', __('lang_v1.to_store'). ':', []) !!}
                    {!! Form::select('filter_to_store', [], null, ['class' => 'form-control select2',
                    'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_category_id', __( 'lang_v1.category' ) . ':*') !!}
                    {!! Form::select('filter_category_id', $categories, null,
                    ['placeholder'
                    => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width:
                    100%;']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_sub_category_id', __( 'lang_v1.sub_category' ) . ':*') !!}
                    {!! Form::select('filter_sub_category_id', $sub_categories, null,
                    ['placeholder'
                    => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width:
                    100%;']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_from_variation_id', __( 'lang_v1.product_from' ) . ':*') !!}
                    {!! Form::select('filter_from_variation_id', $variations, null,
                    ['placeholder'
                    => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width:
                    100%;']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_to_variation_id', __( 'lang_v1.product_to' ) . ':*') !!}
                    {!! Form::select('filter_to_variation_id', $variations, null,
                    ['placeholder'
                    => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width:
                    100%;']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_date_range', 'readonly']); !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_variation_transfer')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{action('VariationTransferController@create')}}" data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
            </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="variation_transfer_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="notexport">@lang('lang_v1.action')</th>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.location_from')</th>
                            <th>@lang('lang_v1.location_to')</th>
                            <th>@lang('lang_v1.from_store')</th>
                            <th>@lang('lang_v1.to_store')</th>
                            <th>@lang('lang_v1.category')</th>
                            <th>@lang('lang_v1.sub_category')</th>
                            <th>@lang('lang_v1.product_from')</th>
                            <th>@lang('lang_v1.product_to')</th>
                            <th>@lang('lang_v1.qty')</th>
                            <th>@lang('lang_v1.cost')</th>
                            <th>@lang('lang_v1.total_cost')</th>
                            <th>@lang('lang_v1.user')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->