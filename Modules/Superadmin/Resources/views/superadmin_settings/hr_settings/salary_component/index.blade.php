<div class="pos-tab-content @if(session('status.tab') == 'salary_component') active @endif">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __(
                'hr::lang.all_salary_component')])
                @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-primary btn-modal pull-right" id="add_salary_component_btn"
                        data-href="{{action('\Modules\HR\Http\Controllers\SalaryComponentController@create')}}"
                        data-container=".salary_component_model">
                        <i class="fa fa-plus"></i> @lang( 'hr::lang.add' )</button>
                </div>
                @endslot
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="salary_component_table"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'hr::lang.name' )</th>
                                    <th>@lang( 'hr::lang.type' )</th>
                                    <th>@lang( 'hr::lang.total_payable' )</th>
                                    <th>@lang( 'hr::lang.cost_to_company' )</th>
                                    <th>@lang( 'hr::lang.rules' )</th>
                                    <th>@lang( 'hr::lang.component_amount' )</th>
                                    <th>@lang( 'hr::lang.statutory_fund' )</th>
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