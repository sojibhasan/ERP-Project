<div class="modal-dialog" role="document" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="box-title">@lang('hr::lang.new_reimbursement_form') </h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="panel-body">
                        <!-- View massage -->
                        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\ReimbursementController@store'),
                        'method' => 'post', 'id' => 'add_form' ]) !!}
                        <div class="panel_controls">
                            @csrf
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">@lang('hr::lang.date')<span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input id="datepicker" type="text" name="date" class="form-control "
                                                value="">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </br>

                            <div class="row">
                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">@lang('hr::lang.department')
                                        <span class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="department_id" id="department" class="form-control "
                                            onchange="get_employee(this.value)">
                                            <option value="">@lang('hr::lang.select_department') ...</option>
                                            <?php foreach ($all_department as $v_department) : ?>
                                            <option value="<?php echo $v_department->id ?>">
                                                <?php echo $v_department->department ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">@lang('hr::lang.employee') <span
                                            class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <select class="form-control select2 " name="employee_id" id="employee">
                                            <option value="">@lang('hr::lang.please_select') </option>
                                            <?php foreach($employee as $item){ ?>
                                            <option value="<?php echo $item->id ?>">
                                                <?php echo  $item->first_name.' '.$item->last_name ?>
                                            </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">@lang('hr::lang.amount') <span
                                            class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <input type="number" class="form-control " name="amount" value="">
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">@lang('hr::lang.description') <span
                                            class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <textarea class="form-control " rows="8" name="memo"></textarea>
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="form-group pull-right" style="margin-right: 30px;">
                                    <div class="col-sm-offset-2 col-sm-5">
                                        <button class="btn btn-primary" type="submit" id="sbtn"><i
                                                class="fa fa-save"></i> @lang('hr::lang.save')
                                            @lang('reimbursement')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();

    $('.select2').css('width','100%');
    $('#datepicker').datepicker();

    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });


</script>