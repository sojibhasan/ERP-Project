<div class="pos-tab-content @if(session('status.tab') == 'working_shift') active @endif">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title"><label>@lang('hr::lang.workshift_list')</label></h3>
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                            data-target="#working_shift_modal">@lang('hr::lang.add_workshift')</button>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="msg"></div>
                                <table id="workshift_table" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('hr::lang.shift_name')</th>
                                            <th>@lang('hr::lang.shift_to')</th>
                                            <th>@lang('hr::lang.shift_from')</th>
                                            <th style="width:125px;">@lang('hr::lang.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>