<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title"><label>@lang('hr::lang.department_list')</label></h3>
                    <button type="button" class="btn btn-info pull-right" data-toggle="modal"
                        data-target="#department_modal">@lang('hr::lang.add_department')</button>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="msg"></div>
                            <table id="department_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('hr::lang.name')</th>
                                        <th>@lang('hr::lang.description')</th>
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