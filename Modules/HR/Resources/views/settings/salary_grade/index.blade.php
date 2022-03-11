<div class="pos-tab-content @if(session('status.tab') == 'salary_grade') active @endif">
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'hr::lang.all_salary_grade')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_salary_grade_btn"
                    data-href="{{action('\Modules\HR\Http\Controllers\SalrayGradeController@create')}}"
                    data-container=".salary_grade_model">
                    <i class="fa fa-plus"></i> @lang( 'hr::lang.add' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="salary_grade_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'hr::lang.salary_grade' )</th>
                                <th>@lang( 'hr::lang.min_salary' )</th>
                                <th>@lang( 'hr::lang.max_salary' )</th>
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
</div>
