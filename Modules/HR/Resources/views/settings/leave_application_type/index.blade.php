<div class="pos-tab-content @if(session('status.tab') == 'leave_application_type') active @endif">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __(
                'hr::lang.all_leave_application_type')])
                @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-primary btn-modal pull-right"
                        id="add_leave_application_type_btn"
                        data-href="{{action('\Modules\HR\Http\Controllers\LeaveApplicationTypeController@create')}}"
                        data-container=".leave_application_type_model">
                        <i class="fa fa-plus"></i> @lang( 'hr::lang.add' )</button>
                </div>
                @endslot
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="leave_application_type_table"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'hr::lang.leave_type' )</th>
                                    <th>@lang( 'hr::lang.allowed_days' )</th>
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
</div>