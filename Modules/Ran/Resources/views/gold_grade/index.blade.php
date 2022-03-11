<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_gold_grade', __('ran::lang.date_range') . ':') !!}
                    {!! Form::text('date_range_gold_grade', null, ['id' => 'date_range_gold_grade', 'class' =>
                    'form-control', 'style' => 'width:100%', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'ran::lang.all_gold_grades')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_gold_grade_btn"
                    data-href="{{action('\Modules\Ran\Http\Controllers\GoldGradeController@create')}}"
                    data-container=".gold_grade_model">
                    <i class="fa fa-plus"></i> @lang( 'ran::lang.add' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="gold_grade_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'ran::lang.date_and_time' )</th>
                                <th>@lang( 'ran::lang.gold_grad_kt' )</th>
                                <th>@lang( 'ran::lang.last_gold_purity' )</th>
                                <th>@lang( 'ran::lang.current_gold_purity' )</th>
                                <th>@lang( 'ran::lang.created_by' )</th>
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

