<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter_category', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter_category', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter_category', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district_fitler_category', __( 'leads::lang.categories' )) !!}
                    {!! Form::select('district_fitler_category', $categories, null, ['class' => 'form-control select2',
                    'style' => 'width: 100%;',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'district_fitler_category']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('users_fitler_category', __( 'leads::lang.user' )) !!}
                    {!! Form::select('users_fitler_category', $users, null, ['class' => 'form-control select2', 'style'
                    => 'width: 100%;',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'users_fitler_category']);
                    !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'leads::lang.all_categories')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_category_btn"
                    data-href="{{action('\Modules\Leads\Http\Controllers\CategoryController@create')}}"
                    data-container=".category_model">
                    <i class="fa fa-plus"></i> @lang( 'leads::lang.add_category' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="leads_category_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'leads::lang.date' )</th>
                                <th>@lang( 'leads::lang.category' )</th>
                                <th>@lang( 'leads::lang.user' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->