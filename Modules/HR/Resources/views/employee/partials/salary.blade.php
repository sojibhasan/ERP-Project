<div class="pos-tab-content">
    <div class="row">
        
        <div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="well well-lg">
						<h4>@lang('hr::lang.salary_type') </h4> <br>
						<input type="radio" name="type" value="Monthly" <?php if(!empty($empSalary)) echo $empSalary->type == 'Monthly'?'checked':'checked'?> onclick="show_monthly();"> @lang('hr::lang.monthly_salary') </input> &nbsp;&nbsp;&nbsp;
						<input type="radio" name="type" value="Hourly" <?php if(!empty($empSalary)) echo $empSalary->type == 'Hourly'?'checked':''?> onclick="show_hourly();"> @lang('hr::lang.hourly_salary') </input>
					</div>
				</div>
			</div>

			<div id="hourly" style="display: <?php if(!empty($empSalary)) {echo $empSalary->type == 'Hourly'?'block':'none';}else{ echo 'none';}?>">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<label>@lang('hr::lang.hourly_salary')  <span class="required">*</span></label>
							<input type="text" class="form-control" name="hourly_salary" value="<?php if(!empty($empSalary)) echo $empSalary->hourly_salary ?>">
						</div>
					</div>
				</div>
			</div>

			<div id="monthly" style="display: <?php if(!empty($empSalary)){ echo $empSalary->type == 'Monthly'?'block':'none';}else{ echo 'none';}?>">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<label>@lang('hr::lang.pay_grade') <span class="required">*</span></label>
							<select class="form-control" name="grade_id" onchange="get_salaryRange(this.value)" id="salaryGrade" >
								<option value="" >@lang('hr::lang.please_select') </option>
								<?php foreach($gradeList as $item):?>
									<option value="<?php echo $item->id ?>" <?php if(!empty($empSalary->grade_id)) echo $item->id == $empSalary->grade_id ?'selected':'' ?> ><?php echo $item->grade_name ?></option>
								<?php endforeach; ?>
							</select>
							<span id="resultSalaryRange"></span>
							<input type="hidden" id="min_salary">
							<input type="hidden" id="max_salary">
						</div>
						<div class="form-group">
							<label>@lang('hr::lang.comment') </label>
							<textarea class="form-control" name="comment"><?php if(!empty($empSalary->comment)) echo $empSalary->comment ?></textarea>
						</div>
					</div>
				</div>

				<?php /* All Earning */ ?>
				<br/><br/>
				<h4>@lang('hr::lang.salary_all_earnings') </h4><hr/>
				<div class="row">
					<div class="col-md-8">
						<?php 
						if(count($salaryEarningList)): 
							foreach($salaryEarningList as $earning):
								$salary = '';
								if(!empty($empSalaryDetails)) {
									foreach ($empSalaryDetails as $key => $details) {
										if ($earning->id == $key) {
											$salary = $details;
										}
									}
								}
								?>
								<div class="row">
									<div class="col-sm-6"><?php echo $earning->component_name ?><strong>(<?php echo $earning->value_type ==1 ? __('amount'): __('percentage') ?>)</strong></div>
									<div class="col-sm-6">
										<div class="form-group">
											<input type="text" name="<?php echo $earning->id ?>" class="form-control key earning" id="<?php echo 'earn'.$earning->id ?>"
												   value="<?php if(!empty($salary)){ echo $salary ;}?>" >
											<span class="required" id="errorSalaryRange"></span>
										</div>
										<input type="hidden" value="<?php echo $earning->type?>" id="<?php echo 'type'.$earning->id ?>">
										<input type="hidden" value="<?php echo $earning->total_payable?>" id="<?php echo 'pay'.$earning->id ?>">
										<input type="hidden" value="<?php echo $earning->cost_company?>" id="<?php echo 'cost'.$earning->id ?>">
										<input type="hidden" value="<?php echo $earning->flag?>" id="<?php echo 'flag'.$earning->id ?>">
										<input type="hidden" value="<?php echo $earning->value_type?>" id="<?php echo 'valueType'.$earning->id ?>">
										<input type="hidden" name="earn[]" value="<?php echo $earning->id ?>">
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif ?>
					</div>
				</div>

				<?php /* All Deduction */ ?>
				<br/><br/>
				<h4>@lang('hr::lang.salary_all_deductions') </h4><hr/>
				<div class="row">
					<div class="col-md-8">
						<?php if(count($salaryDeductionList)): 
							foreach($salaryDeductionList as $deduction):
								$salary = '';
								if(!empty($empSalaryDetails)) {
									foreach ($empSalaryDetails as $key => $details) {
										if ($deduction->id == $key) {
											$salary = $details;
										}
									}
								}
								?>
								<div class="row">
									<div class="col-sm-6"><?php echo $deduction->component_name?> <strong>(<?php echo $deduction->value_type ==1 ? __('amount'): __('percentage') ?>)</strong></div>
									<div class="col-sm-6">
										<div class="form-group" >
											<input type="text"  name="<?php echo $deduction->id ?>" class="form-control key deduction" id="<?php echo 'earn'.$deduction->id ?>" value="<?php if(!empty($salary)){ echo $salary ;} ?>">
										</div>
										<input type="hidden" value="<?php echo $deduction->type?>" id="<?php echo 'type'.$deduction->id ?>">
										<input type="hidden" value="<?php echo $deduction->total_payable?>" id="<?php echo 'pay'.$deduction->id ?>">
										<input type="hidden" value="<?php echo $deduction->cost_company?>" id="<?php echo 'cost'.$deduction->id ?>">
										<input type="hidden" value="<?php echo $deduction->flag?>" id="<?php echo 'flag'.$deduction->id ?>">
										<input type="hidden" value="<?php echo $deduction->value_type?>" id="<?php echo 'valueType'.$deduction->id ?>">
										<input type="hidden" name="deduction[]" value="<?php echo $deduction->id ?>">
									</div>
								</div>
							<?php endforeach; 
						endif ?>
					</div>
				</div>

				<?php /* Statutory Payment */ ?>
				<br/><br/>
				<h4>@lang('hr::lang.statutory_payment_salary') </h4><hr/>
				<div class="row">
					<div class="col-md-8">
						<?php if(count($statutoryPaymentsList)): 
							foreach($statutoryPaymentsList as $statutory):
								$salary = '';
								if(!empty($empSalaryDetails)) {
									foreach ($empSalaryDetails as $key => $details) {
										if ($statutory->id == $key) {
											$salary = $details;
										}
									}
								}
								?>
								<div class="row">
									<div class="col-sm-6"><?php echo $statutory->component_name?> <strong>(<?php echo $statutory->value_type ==1 ? __('amount'): __('percentage') ?>)</strong></div>
									<div class="col-sm-6">
										<div class="form-group" >
											<input type="text"  name="<?php echo $statutory->id ?>" class="form-control key statutory" id="<?php echo 'earn'.$statutory->id ?>" value="<?php if(!empty($salary)){ echo $salary;} ?>">
										</div>
										<input type="hidden" value="<?php echo $statutory->type?>" id="<?php echo 'type'.$statutory->id ?>">
										<input type="hidden" value="<?php echo $statutory->total_payable?>" id="<?php echo 'pay'.$statutory->id ?>">
										<input type="hidden" value="<?php echo $statutory->cost_company?>" id="<?php echo 'cost'.$statutory->id ?>">
										<input type="hidden" value="<?php echo $statutory->flag?>" id="<?php echo 'flag'.$statutory->id ?>">
										<input type="hidden" value="<?php echo $statutory->value_type?>" id="<?php echo 'valueType'.$statutory->id ?>">
										<input type="hidden" name="statutory[]" value="<?php echo $statutory->id ?>">
									</div>
								</div>
							<?php endforeach; 
						endif ?>
					</div>
				</div>

				<?php /* All Earning */ ?>
				<br/><br/>
				<h4>@lang('hr::lang.salary_summary') </h4><hr/>
				<div class="well well-sm">
					<div class="row">
						<div class="col-md-8">
							<div class="row" style="padding-bottom: 15px">
								<div class="col-sm-6">@lang('hr::lang.total_deductions')  :</div>
								<div class="col-sm-6" id="resultTotalDeduction"><strong><?php if(!empty($empSalary->total_deduction)){ echo $currecy_symbol.' '.$empSalary->total_deduction ; }else{ echo $currecy_symbol.' '.'00' ;} ?></strong></div>
								<input type="hidden" name="total_deduction" id="totalDeduction" value="<?php if(!empty($empSalary->total_deduction)){ echo $empSalary->total_deduction ; }else{ echo '0' ;} ?>">
							</div>
							<div class="row" style="padding-bottom: 15px">
								<div class="col-sm-6">@lang('hr::lang.total_payable')  :</div>
								<div class="col-sm-6" id="resultTotalPayable"><strong><?php if(!empty($empSalary->total_payable)){ echo $currecy_symbol.' '.$empSalary->total_payable ; }else{ echo $currecy_symbol.' '.'00' ;} ?></strong></div>
								<input type="hidden" name="total_payable" id="totalPayable" value="<?php if(!empty($empSalary->total_payable)){ echo $empSalary->total_payable ; }else{ echo '0' ;} ?>">
							</div>
							<div class="row" style="padding-bottom: 15px">
								<div class="col-sm-6">@lang('hr::lang.cost_to_the_company')  :</div>
								<div class="col-sm-6" id="resultCostToCompany"><strong><?php if(!empty($empSalary->total_cost_company)){ echo $currecy_symbol.' '. $empSalary->total_cost_company ; }else{ echo $currecy_symbol.' '.'00' ;} ?></strong></div>
								<input type="hidden" name="total_cost_company" id="totalCostCompany" value="<?php if(!empty($empSalary->total_cost_company)){ echo $empSalary->total_cost_company ; }else{ echo '0' ;} ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="salary_id" value="<?php if(!empty($empSalary->id))echo $empSalary->id ?>" >
			<input type="hidden" id="field" value="0">
			<input type="hidden" id="tempDeduction">
			<input type="hidden" id="tempPayable">
			<input type="hidden" id="tempCostCompany">
			
		</div>
		
	</div>
   </div>
