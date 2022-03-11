<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmployeeController@store'), 'method' => 'post',
        'id' => 'add_employee_form', 'files' => true ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'hr::lang.add_employee' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_number') <span class="required"
                                aria-required="true">*</span></label>
                        <input type="text" name="employee_number" class="form-control" value="{{$employee_number}}"
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.business_location') <span class="required"
                                aria-required="true">*</span></label>
                        <select name="business_location" id="business_location" aria-placeholder="Please Select"
                            class="form-control select2">
                            <option value="">Please Select</option>
                            @foreach ($locations as $location)
                            <option value="{{$location->id}}">{{$location->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.first_name') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="first_name" class="form-control ">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.last_name')<span class="required" aria-required="true">*</span></label>
                        <input type="text" name="last_name" class="form-control ">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group form-group-bottom">
                        <label>@lang('hr::lang.date_of_birth')<span class="required"
                                aria-required="true">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control " id="datepicker" name="date_of_birth"
                                data-date-format="yyyy/mm/dd">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.department')</label>
                        {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.please_select')]) !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.marital_status')</label>
                        <select class="form-control " name="marital_status">
                            <option value="">@lang('hr::lang.please_select')..</option>
                            <option value="Singel">@lang('hr::lang.singel')</option>
                            <option value="Married">@lang('hr::lang.married')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.country')<span class="required" aria-required="true">*</span></label>
                        <select class="form-control select2" name="country">
                            <option value="">@lang('hr::lang.please_select')</option>
                            <?php foreach($countries as $item){ ?>
                            <option value="<?php echo $item->country ?>"><?php echo $item->country ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.blood_group')</label>
                        {!! Form::select('blood_group', ['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', 'Do Not Know'
                        => 'Do Not Know'], null, ['class' => 'form-control select2', 'pleceholder' =>
                        'lang_v1.please_select']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.id_number')</label>
                        <input type="text" name="id_number" class="form-control ">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.religious')</label>
                        {!! Form::select('religious', $religions, null, ['class' => 'form-control', 'placeholder' =>
                        __('lang_v1.please_select')]) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.password') *</label>
                        <small>@lang('hr::lang.at_least_6_characters')</small>
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>@lang('hr::lang.gender')<span class="required" aria-required="true">*</span></label>
                        <label class="css-input css-radio css-radio-success push-10-r">
                            <input name="gender" value="Male" checked=""
                                type="radio"><span></span>@lang('hr::lang.male')
                        </label>
                        <label class="css-input css-radio css-radio-success push-10-r">
                            <input name="gender" value="Female" type="radio"><span></span>@lang('hr::lang.female')
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.photograph')</label>
                    </div>
                </div>

                <div class="col-md-6 equal-column">
                    <div class="form-group">
                        {!! Form::label('employee_photo', __('hr::lang..employee_photo') . ':') !!}
                        {!! Form::file('employee_photo', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                        <small>
                        <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') /
                            1000000)])  @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
                        </small>
                    </div>
                </div>
                <p class="text-muted"><span class="required"
                        aria-required="true">*</span>@lang('hr::lang.required_field')</p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#datepicker').datepicker();
    var img_fileinput_setting = {
        showUpload: false,
        showPreview: true,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
        previewSettings: {
            image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
        },
    };
    $('#upload_image').fileinput(img_fileinput_setting);
</script>