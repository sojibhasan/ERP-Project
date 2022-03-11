
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <form id="SalaryForm" action="{{action('\Modules\HR\Http\Controllers\SalaryController@store')}}"
                method="post">
                @csrf
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title">@lang('hr::lang.employee_salary_details')</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="well well-lg" style="display: inline-block;">
                                    <div class="col-md-4">
                                        <h4>@lang('hr::lang.salary_type') </h4> <br>
                                        <input type="radio" name="type" value="Monthly" onclick="show_monthly();">
                                        @lang('hr::lang.monthly_salary') &nbsp;&nbsp;&nbsp;
                                        <input type="radio" name="type" value="Hourly" onclick="show_hourly();">
                                        @lang('hr::lang.hourly_salary')
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group margin">
                                            <label class="col-sm-4 control-label">@lang('hr::lang.year') <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" name="year" class="form-control years"
                                                        value="{{date('Y')}}" autocomplete="off">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group margin">
                                            <label class="col-sm-4 control-label">@lang('hr::lang.month') <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" name="month" class="form-control months"
                                                        value="<?php echo date('m');?>" autocomplete="off">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="hourly" style="display:none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('hr::lang.employee') <span class="required">*</span></label>
                                        <select class="form-control select2" name="employee_id_hourly"
                                            onchange="get_basicSalary(this.value)" id="employeed" style="width:100%">
                                            <option value="">@lang('hr::lang.please_select') </option>
                                            <?php foreach($employees as $employee): ?>
                                            <option value="<?php echo $employee->id ?>">
                                                <?php echo $employee->first_name . $employee->last_name; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('hr::lang.hourly_salary') <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="hourly_salary">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="monthly" style="display:none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('hr::lang.employee') <span class="required">*</span></label>
                                        <select class="form-control select2" name="employee_id_monthly"
                                            onchange="get_basicSalary(this.value)" id="employeed" style="width:100%">
                                            <option value="">@lang('hr::lang.please_select') </option>
                                            <?php foreach($employees as $employee): ?>
                                            <option value="<?php echo $employee->id ?>">
                                                <?php echo $employee->first_name . $employee->last_name; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('hr::lang.comment') </label>
                                        <textarea class="form-control" name="comment"></textarea>
                                    </div>
                                </div>
                            </div>

                            <?php /* All Earning */ ?>
                            <br /><br />
                            <h4>@lang('hr::lang.salary_all_earnings') </h4>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <?php 
                                        if(count($salary_earning_list)): 
                                            foreach($salary_earning_list as $earning):
                                                $salary = '';
                                                if(!empty($empSalaryDetails)) {
                                                    foreach ($empSalaryDetails as $key => $details) {
                                                        if ($earning->id == $key) {
                                                            $salary = $details;
                                                        }
                                                    }
                                                }
                                                ?>
                                    <div class="col-md-3">
                                        <label for="">
                                            <?php echo $earning->component_name ?><strong>(<?php echo $earning->value_type ==1 ? __('amount'): __('percentage') ?>)</strong>
                                        </label>

                                        <div class="form-group">
                                            <input type="text" name="<?php echo $earning->id ?>"
                                                class="form-control key earning <?php if($earning->component_name == "Basic Salary"){ echo 'earningbasic'; } ?>"
                                                id="<?php echo 'earn'.$earning->id ?>"
                                                value="<?php if(!empty($salary)){ echo $salary ;}?>">
                                            <span class="required" id="errorSalaryRange"></span>
                                        </div>
                                        <input type="hidden" value="<?php echo $earning->type?>"
                                            id="<?php echo 'type'.$earning->id ?>">
                                        <input type="hidden" value="<?php echo $earning->total_payable?>"
                                            id="<?php echo 'pay'.$earning->id ?>">
                                        <input type="hidden" value="<?php echo $earning->cost_company?>"
                                            id="<?php echo 'cost'.$earning->id ?>">
                                        <input type="hidden" value="<?php echo $earning->flag?>"
                                            id="<?php echo 'flag'.$earning->id ?>">
                                        <input type="hidden" value="<?php echo $earning->value_type?>"
                                            id="<?php echo 'valueType'.$earning->id ?>">
                                        <input type="hidden" name="earn[]" value="<?php echo $earning->id ?>">
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif ?>
                                </div>
                            </div>

                            <?php /* All Deduction */ ?>
                            <br /><br />
                            <h4>@lang('hr::lang.salary_all_deductions') </h4>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if(count($salary_deduction_list)): 
                                            foreach($salary_deduction_list as $deduction):
                                                $salary = '';
                                                if(!empty($empSalaryDetails)) {
                                                    foreach ($empSalaryDetails as $key => $details) {
                                                        if ($deduction->id == $key) {
                                                            $salary = $details;
                                                        }
                                                    }
                                                }
                                                ?>
                                    <div class="col-md-3">
                                        <label for="">
                                            <?php echo $deduction->component_name?><strong>(<?php echo $deduction->value_type ==1 ? __('amount'): __('percentage') ?>)</strong>
                                        </label>

                                        <div class="form-group">
                                            <input type="text" name="<?php echo $deduction->id ?>"
                                                class="form-control key deduction"
                                                id="<?php echo 'earn'.$deduction->id ?>"
                                                value="<?php if(!empty($salary)){ echo $salary ;} ?>">
                                        </div>
                                        <input type="hidden" value="<?php echo $deduction->type?>"
                                            id="<?php echo 'type'.$deduction->id ?>">
                                        <input type="hidden" value="<?php echo $deduction->total_payable?>"
                                            id="<?php echo 'pay'.$deduction->id ?>">
                                        <input type="hidden" value="<?php echo $deduction->cost_company?>"
                                            id="<?php echo 'cost'.$deduction->id ?>">
                                        <input type="hidden" value="<?php echo $deduction->flag?>"
                                            id="<?php echo 'flag'.$deduction->id ?>">
                                        <input type="hidden" value="<?php echo $deduction->value_type?>"
                                            id="<?php echo 'valueType'.$deduction->id ?>">
                                        <input type="hidden" name="deduction[]" value="<?php echo $deduction->id ?>">
                                    </div>

                                    <?php endforeach; 
                                        endif ?>
                                </div>
                            </div>

                            <?php /* Statutory Payment */ ?>
                            <br /><br />
                            <h4>@lang('hr::lang.statutory_payment_salary') </h4>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if(count($statutory_payments_list)): 
                                            foreach($statutory_payments_list as $statutory):
                                                $salary = '';
                                                if(!empty($empSalaryDetails)) {
                                                    foreach ($empSalaryDetails as $key => $details) {
                                                        if ($statutory->id == $key) {
                                                            $salary = $details;
                                                        }
                                                    }
                                                }
                                                ?>
                                    <div class="col-md-3 cmd-3">
                                        <label for="">
                                            <?php echo $statutory->component_name?><strong>(<?php echo $statutory->value_type ==1 ? __('amount'): __('percentage') ?>)</strong>
                                        </label>

                                        <div class="form-group">
                                            <input type="text" name="<?php echo $statutory->id ?>"
                                                class="form-control key statutory"
                                                id="<?php echo 'earn'.$statutory->id ?>"
                                                value="<?php if(!empty($salary)){ echo $salary ;} ?>">
                                        </div>
                                        <input type="hidden" value="<?php echo $statutory->type?>"
                                            id="<?php echo 'type'.$statutory->id ?>">
                                        <input type="hidden" value="<?php echo $statutory->total_payable?>"
                                            id="<?php echo 'pay'.$statutory->id ?>">
                                        <input type="hidden" value="<?php echo $statutory->cost_company?>"
                                            id="<?php echo 'cost'.$statutory->id ?>">
                                        <input type="hidden" value="<?php echo $statutory->flag?>"
                                            id="<?php echo 'flag'.$statutory->id ?>">
                                        <input type="hidden" value="<?php echo $statutory->value_type?>"
                                            id="<?php echo 'valueType'.$statutory->id ?>">
                                        <input type="hidden" name="statutory[]" value="<?php echo $statutory->id ?>">
                                    </div>
                                    <?php endforeach; 
                                        endif ?>
                                </div>
                            </div>

                            <?php /* All Earning */ ?>
                            <br /><br />
                            <h4>@lang('hr::lang.salary_summary') </h4>
                            <hr />
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="row" style="padding-bottom: 15px">
                                            <div class="col-sm-6">@lang('hr::lang.total_deductions') :</div>
                                            <div class="col-sm-6" id="resultTotalDeduction">
                                                <strong></strong>
                                            </div>
                                            <input type="hidden" name="total_deduction" id="totalDeduction" value="">
                                        </div>
                                        <div class="row" style="padding-bottom: 15px">
                                            <div class="col-sm-6">@lang('hr::lang.total_payable') :</div>
                                            <div class="col-sm-6" id="resultTotalPayable">
                                                <strong></strong>
                                            </div>
                                            <input type="hidden" name="total_payable" id="totalPayable" value="">
                                        </div>
                                        <div class="row" style="padding-bottom: 15px">
                                            <div class="col-sm-6">@lang('hr::lang.cost_to_the_company') :</div>
                                            <div class="col-sm-6" id="resultCostToCompany">
                                                <strong></strong>
                                            </div>
                                            <input type="hidden" name="total_cost_company" id="totalCostCompany"
                                                value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="field" value="0">
                        <input type="hidden" id="tempDeduction">
                        <input type="hidden" id="tempPayable">
                        <input type="hidden" id="tempCostCompany">
                        <input type="hidden" name="id" id="emp_id" value="">
                        <input type="hidden" name="salary_id" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
