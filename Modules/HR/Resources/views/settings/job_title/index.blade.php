<div class="pos-tab-content @if(session('status.tab') == 'job_title') active @endif">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title"><label>@lang('hr::lang.job_title_list')</label></h3>
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                            data-target="#job_title_modal">@lang('hr::lang.add_job_title')</button>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="msg"></div>
                                <table id="jobtitle_table" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('hr::lang.job_title')</th>
                                            <th>@lang('hr::lang.job_description')</th>
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