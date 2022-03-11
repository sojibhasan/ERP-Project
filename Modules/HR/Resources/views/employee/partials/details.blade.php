<div class="pos-tab-content active">
    <h3 style="margin-top: 5px;">Personal details</h3>
    <div class="row well mr-3" style="margin-right: 5px;">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.employee_number')<span class="required"
                                        aria-required="true">*</span></label>
                                <input type="text" name="employee_number" readonly
                                    value="{{$employee->employee_number}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.first_name')<span class="required"
                                        aria-required="true">*</span></label>
                                <input type="text" name="first_name" value="{{$employee->first_name}}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.last_name')<span class="required" aria-required="true">*</span></label>
                                <input type="text" name="last_name" value="{{$employee->last_name}}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- /.Start Date -->
                            <div class="form-group form-group-bottom">
                                <label>@lang('hr::lang.date_of_birth')<span class="required"
                                        aria-required="true">*</span></label>

                                <div class="input-group">
                                    <input type="text" class="form-control" id="datepicker" name="date_of_birth"
                                        value="<?php echo $employee->date_of_birth ?>" data-date-format="yyyy/mm/dd">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.marital_status')</label>
                                <select class="form-control" name="marital_status">
                                    <option value="">@lang('hr::lang.please_select')..</option>
                                    <option value="Singel"
                                        <?php if(!empty($employee->marital_status)) echo $employee->marital_status == 'Singel'?'selected':''  ?>>
                                        @lang('hr::lang.singel') </option>
                                    <option value="Married"
                                        <?php if(!empty($employee->marital_status)) echo $employee->marital_status == 'Married'?'selected':''  ?>>
                                        @lang('hr::lang.married') </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.country')<span class="required" aria-required="true">*</span></label>
                                <select class="form-control" name="country">
                                    <option value="">@lang('hr::lang.please_select')..</option>
                                    <?php foreach($countries as $item){ ?>
                                    <option value="<?php echo $item->country ?>"
                                        <?php if(!empty($employee->country)) echo $employee->country == $item->country ?'selected':''  ?>>
                                        <?php echo $item->country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('hr::lang.blood_group') </label>
                                {!! Form::select('blood_group', ['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', 'Do Not Know' => 'Do Not Know'], $employee->blood_group, ['class' => 'form-control select2', 'pleceholder' => 'lang_v1.please_select']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('hr::lang.id_number') </label>
                        <input type="text" name="id_number"
                            value="<?php if(!empty($employee->id_number)) echo $employee->id_number ?>"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>@lang('hr::lang.religious') </label>
                        <select class="form-control" name="religious">
                            <option value="">@lang('hr::lang.please_select')..</option>
                            <?php foreach($religions as $religion){ ?>
                            <option value="<?php echo $religion->religion_name ?>"
                                <?php if(!empty($employee->religious)) echo $employee->religious == $religion->religion_name ?'selected':''  ?>>
                                <?php echo $religion->religion_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('hr::lang.gender')<span class="required" aria-required="true">*</span></label>
                                <label class="css-input css-radio css-radio-success push-10-r">
                                    <input name="gender" value="Male"
                                        <?php echo $employee->gender == 'Male'?'checked':''  ?>
                                        type="radio"><span></span>@lang('hr::lang.male')
                                </label>
                                <label class="css-input css-radio css-radio-success push-10-r">
                                    <input name="gender" value="Female"
                                        <?php echo $employee->gender == 'Female'?'checked':''  ?>
                                        type="radio"><span></span>@lang('hr::lang.female')
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.Employee Image -->
                    <div class="col-md-6 equal-column">
                        <div class="form-group">
                            {!! Form::label('employee_photo', __('hr::lang.employee_photo') . ':') !!}
                            {!! Form::file('employee_photo', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                            <small>
                            <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') /
                                1000000)])  @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6 equal-column">
                        <img src="{{url($employee->photo)}}" style="width:100%; height: 100%;" alt="Employee Photo"> 
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- CONTACT DETAILS -->
    @php
        $contact_details = json_decode($employee->contact_details);
    @endphp
    <h3 style="margin-top: 5px;">Contact details</h3>
    <div class="row well mr-3" style="margin-right: 5px;">
        <div class="row">
            <div class="col-md-6">

                <div class="form-group">
                    <label>@lang('hr::lang.address_street_1')<span class="required">*</span></label>
                    <input type="text" name="address_1" class="form-control"
                        value="<?php if(!empty($contact_details->address_1)){echo $contact_details->address_1; }?>">
                </div>


                <div class="form-group">
                    <label>@lang('hr::lang.city') <span class="required">*</span></label>
                    <input type="text" name="city" class="form-control"
                        value="<?php if(!empty($contact_details->city)){echo $contact_details->city; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.zip_postal_code') <span class="required">*</span></label>
                    <input type="text" name="postal" class="form-control"
                        value="<?php if(!empty($contact_details->postal)){echo $contact_details->postal; }?>">
                </div>

                <hr />
                <div class="form-group">
                    <label>@lang('hr::lang.home_telephone') <span class="required">*</span></label>
                    <input type="text" name="home_telephone" class="form-control"
                        value="<?php if(!empty($contact_details->home_telephone)){echo $contact_details->home_telephone; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.work_telephone') </label>
                    <input type="text" name="work_telephone" class="form-control"
                        value="<?php if(!empty($contact_details->home_telephone)){echo $contact_details->home_telephone; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.mobile') </label>
                    <input type="text" name="mobile" class="form-control"
                        value="<?php if(!empty($contact_details->mobile)){echo $contact_details->mobile; }?>">
                </div>

            </div>



            <div class="col-md-6">

                <div class="form-group">
                    <label>@lang('hr::lang.address_street_2') </label>
                    <input type="text" name="address_2" class="form-control"
                        value="<?php if(!empty($contact_details->address_2)){echo $contact_details->address_2; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.state_province') <span class="required">*</span></label>
                    <input type="text" name="state" class="form-control"
                        value="<?php if(!empty($contact_details->state)){echo $contact_details->state; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.country') <span class="required">*</span></label>
                    <select class="form-control" name="country">
                        <option value="">@lang('hr::lang.please_select') ...</option>
                        <?php foreach($countries as $country){ ?>
                        <option value="<?php echo $country->country ?>"
                            <?php if(!empty($contact_details->country)) echo $contact_details->country == $country->country ?'selected':''  ?>>
                            <?php echo $country->country ?></option>
                        <?php } ?>
                    </select>
                </div>

                <hr />
                <div class="form-group">
                    <label>@lang('hr::lang.work_email') </label>
                    <input type="text" name="work_email" class="form-control"
                        value="<?php if(!empty($contact_details->work_email)){echo $contact_details->work_email; }?>">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.other_email') </label>
                    <input type="text" name="other_email" class="form-control"
                        value="<?php if(!empty($contact_details->other_email)){echo $contact_details->other_email; }?>">
                </div>



            </div>

        </div>

    </div>


    <!-- Direct Deposit -->
    @php
        $deposit = json_decode($employee->deposit);
    @endphp
    <h3 style="margin-top: 5px;">Direct Deposit</h3>
    <div class="row well mr-3" style="margin-right: 5px;">

        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.account_name') <span class="required">*</span></label>
                        <input type="text" class="form-control" name="account_name"
                            value="<?php if(!empty($deposit->account_name)) echo $deposit->account_name ?>">

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.account_number') <span class="required">*</span></label>
                        <input type="text" class="form-control" name="account_number"
                            value="<?php if(!empty($deposit->account_number)) echo $deposit->account_number ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.bank_name') <span class="required">*</span></label>
                        <input type="text" name="bank_name" class="form-control"
                            value="<?php if(!empty($deposit->bank_name)) echo $deposit->bank_name ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.note') </label>
                        <textarea class="form-control"
                            name="note"><?php if(!empty($deposit->note)) echo $deposit->note ?></textarea>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>