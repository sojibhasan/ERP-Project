<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'hr::lang.all_employment_status')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_employment_status_btn"
                    data-href="{{action('\Modules\HR\Http\Controllers\EmploymentStatusController@create')}}"
                    data-container=".employment_status_model">
                    <i class="fa fa-plus"></i> @lang( 'hr::lang.add' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="employment_status_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'hr::lang.status_name' )</th>
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