<!-- Main content -->
<section class="content">
    {{-- <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('ran::lang.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['id' => 'date_range', 'class' =>
                    'form-control', 'style' => 'width:100%', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'ran::lang.all_gold_prices')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_gold_price_btn"
                    data-href="{{action('\Modules\Ran\Http\Controllers\GoldPriceController@create')}}"
                    data-container=".gold_price_model">
                    <i class="fa fa-plus"></i> @lang( 'ran::lang.add' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="gold_price_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'ran::lang.date_and_time' )</th>
                                @foreach ($gold_grades as $gold_grade)
                                <th>{{$gold_grade->grade_name}}({{$gold_grade->gold_purity}})</th>
                                @endforeach
                                <th>@lang( 'ran::lang.created_by' )</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($gold_prices->count())
                            @foreach ($gold_prices as $gold_price)
                            <tr>
                                <td>{{$gold_price->date_and_time}}</td>
                                @foreach ($gold_grades as $gold_grade)
                                @if($gold_grade->grade_name == '24')
                                <td>{{@num_format($gold_price->price) }}</td>
                                @else
                                <td>{{ @num_format(($gold_price->price / $gold_price->purity) * $gold_grade->gold_purity) }}</td>
                                @endif
                                @endforeach
                                <td>{{$gold_price->username}}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->