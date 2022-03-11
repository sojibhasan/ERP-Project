<div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">@lang('hr::lang.application_view')</h4>
        </div>

        <div class="modal-body">


            <div class="row">
                <div class="col-sm-12">

                    <div class="row">
                        <div class="col-sm-12" data-offset="0">
                            <div class="wrap-fpanel">
                                <div class="box box-primary" data-collapsed="0">
                                    <div class="panel-body">
                                        {!! Form::open(['id' => 'application_update', 'url' => action('\Modules\HR\Http\Controllers\ApplicationController@update', $application->id), 'mehtod' => 'put']) !!}

                                        <div class="panel_controls">
                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.employee_id')
                                                    :</label>
                                                <div class="col-sm-5" style="padding-top: 5px">
                                                    <?php echo $application->em_id ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.employee_name')
                                                    :</label>
                                                <div class="col-sm-5" style="padding-top: 5px">
                                                    <?php echo  $application->first_name.' '.$application->last_name ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.leave_date') 
                                                    :</label>
                                                <div class="col-sm-5" style="padding-top: 5px">
                                                    <?php echo date('Y-m-d', strtotime($application->start_date)).' To '. date('Y-m-d', strtotime($application->end_date)) ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.leave_type')
                                                    :</label>
                                                <div class="col-sm-5" style="padding-top: 5px">
                                                    <?php echo $application->type_name ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.application_date') 
                                                    :</label>
                                                <div class="col-sm-5" style="padding-top: 5px">
                                                    <?php echo date('Y-m-d', strtotime($application->application_date)) ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-6 control-label">@lang('hr::lang.status') :</label>
                                                <div class="col-sm-3" style="padding-top: 5px">
                                                    <select class="form-control" name="status">
                                                        <option value="pending"
                                                            <?php echo $application->status == 'Pending' ? 'selected':''  ?>>
                                                            @lang('hr::lang.pending')</option>
                                                        <option value="Accepted"
                                                            <?php echo $application->status == 'Accepted' ? 'selected':''  ?>>
                                                            @lang('hr::lang.approved')</option>
                                                        <option value="Rejected"
                                                            <?php echo $application->status == 'Rejected' ? 'selected':''  ?>>
                                                            @lang('hr::lang.reject')</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="hidden" name="id" value="{{$application->id}}">


                                            <div class="form-group no-print">
                                                <div class="col-sm-offset-6 col-sm-6">
                                                    <button type="submit" class=" pull-right btn bg-olive btn-md btn-flat">
                                                        Save</button>
                                                </div>
                                            </div>

                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();

    $('.select2').css('width','100%');


    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });


</script>