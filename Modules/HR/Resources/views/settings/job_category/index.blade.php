<div class="pos-tab-content @if(session('status.tab') == 'job_category') active @endif">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title"><label>@lang('hr::lang.job_categories_list')</label></h3>
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                            data-target="#job_category_modal">@lang('hr::lang.add_job_category')</button>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="jobcategory_table" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('hr::lang.job_categories')</th>
                                            <th style="width:125px;">@lang('hr::lang.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
</div>
