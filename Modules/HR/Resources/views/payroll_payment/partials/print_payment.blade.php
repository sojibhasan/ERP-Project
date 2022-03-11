<style>
    .box-header>.box-tools2 {
        right: 7%;
        margin: 10px;
        position: absolute;
        top: 5px;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12" data-offset="0">
                <div class="wrap-fpanel">
                    <div class="box box-primary" data-collapsed="0">
                        <div class="panel-body" id="double_print">
                            <link href="{{ asset('bootstrap/css/bootstrap.min.css?v='.$asset_v) }}" media="print"
                                rel="stylesheet" type="text/css" />
                            <div class="col-md-12" id="partA">
                                <div align="center">
                                    <h2>{{request()->session()->get('business.name')}}</h2>
                                    <h3>@lang('hr::lang.salary_payslip') </h3>
                                    <p>@lang('hr::lang.salary_month'):
                                        {{$pay_slip->month}}</p>
                                    <br />
                                </div>
                                @if($pay_slip->type == 'Hourly')
                                <p>@lang('hr::lang.date_range'): {{$pay_slip->date_range}}</p>
                                @endif
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <th>@lang('hr::lang.employee') </th>
                                            <td> {{$employee->first_name.' '.$employee->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.employee_number') </th>
                                            <td> {{$employee->employee_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.department') </th>
                                            <td>{{$employee->department }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.job_title') </th>
                                            <td>{{$employee->job_title }}</td>
                                        </tr>
                                        @if($employee->termination == 0)
                                        <tr>
                                            <th>@lang('hr::lang.status') }}</th>
                                            <td><span class="label bg-red">Terminated</span></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <br /><br />
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            @if($pay_slip->type == 'Monthly')
                                            <th>@lang('hr::lang.gross_salary') </th>
                                            <td>{{$pay_slip->gross_salary}}</td>
                                            @else
                                            <th>@lang('hr::lang.hourly_salary') </th>
                                            <td>{{$pay_slip->gross_salary }}</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 55px;">
                                            <th style="vertical-align:middle;font-size:16px;">Salary Deduction</th>
                                            <td>&nbsp;</td>
                                        </tr>
                                        @php
										if(!empty($salaryDeductionList)){
											$totalPDeduction = 0; $totalSPDeduction = 0;
											foreach($salaryDeductionList as $deduction){
												if($deduction->value_type == 1){
													$totalSPDeduction += $deduction->component_amount;
												}else{
													$totalPDeduction += $deduction->component_amount;
												}
										@endphp
                                        <tr>
                                            <th>{{$deduction->component_name}}</th>
                                            <td>{{$deduction->component_amount}}</td>
                                        </tr>
                                        <?php
											}
										}
										?>
                                        <tr>
                                            @if($pay_slip->type == 'Monthly')
                                            <th>@lang('hr::lang.deduction')</th>
                                            <td>{{$pay_slip->deduction}}</td>
                                            @else
                                            <th>@lang('hr::lang.total_hour') </th>
                                            <td>{{$pay_slip->total_hour}}</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.net_salary')</th>
                                            <td>{{$pay_slip->net_salary }}</td>
                                        </tr>
                                        @php
										if(!empty($pay_slip->award)){
											$award = json_decode($pay_slip->award);
										}
										@endphp
                                        @if(!empty($award))
											@foreach($award as $item )
                                        <tr>
                                            <th>@lang('hr::lang.award') </th>
                                            <td>{{$item->award_amount }} ({{$item->award_name}})</td>
                                        </tr>
											@endforeach; 
										@endif 
                                        @if(!empty($pay_slip->fine_deduction))
                                        <tr>
                                            <th>@lang('hr::lang.fine_deduction') </th>
                                            <td>{{$pay_slip->fine_deduction }}</td>
                                        </tr>
                                        @endif

                                        @if(!empty($pay_slip->bonus))
                                        <tr>
                                            <th>@lang('hr::lang.bonus') </th>
                                            <td>{{$pay_slip->bonus }}</td>
                                        </tr>
                                       @endif
                                        <tr style="height: 55px;">
                                            <th style="vertical-align:middle;font-size:16px;">Statutory Payments</th>
                                            <td>&nbsp;</td>
                                        </tr>
                                        @php 
										if(!empty($statutoryPaymentsList)){
											$totalPStatutory = 0; $totalSPStatutory = 0;
											foreach($statutoryPaymentsList as $statutory){
												if($statutory->value_type == 1){
													$totalSPStatutory += $statutory->component_amount;
												}else{
													$totalPStatutory += $statutory->component_amount;
												}
										@endphp
                                        <tr>
                                            <th>{{$statutory->component_name}}</th>
                                            <td>{{$statutory->component_amount}}</td>
                                        </tr>
                                        @php
											}
										}
										@endphp
                                        <tr style="height: 55px;">
                                            <th style="vertical-align:middle;font-size:16px;">
                                                @lang('hr::lang.payment_amount') </th>
                                            @php
											$totalNetPayment1 = 0;
											if($totalPDeduction > 0){
												$totalNetPayment1 = (($pay_slip->gross_salary)*($totalPDeduction))/(100);
											}

											$totalNetPayment2 = 0;
											if($totalPStatutory > 0){
												$totalNetPayment2 = (($pay_slip->gross_salary)*($totalPStatutory))/(100);
											}
											$totalNetPayment = ($pay_slip->net_payment)-($totalSPDeduction)-($totalSPStatutory)-($totalNetPayment1)-($totalNetPayment2);
											@endphp
                                            <td>{{number_format($totalNetPayment,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.payment_method') </th>
                                            <td>{{$pay_slip->payment_method }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('hr::lang.comment') </th>
                                            <td>{{$pay_slip->note }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr />
                                <div>
                                    <p>Received the above Salary with thanks</p><br /><br />
                                    <p style="border-top: 2px dotted #000;width: 25%;text-align: center;">
                                        {{$employee->first_name.' '.$employee->last_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
	$("#printButton").click(function(){
		var mode = 'iframe'; // popup
		var close = mode == "popup";
		var options = { mode : mode, popClose : close};
		if (!$("#multi_print").is(':checked')) {
			$("div#sinngle_print").printArea( options );
		}else{
			$("div#double_print").printArea( options );
		}
	});
});

$(document).ready(function() {
	$('#multi_print').change(function() {
		if (!$(this).is(':checked')) {
			$('#sinngle_print').show();
			$('#double_print').hide();
		}else{
			$('#double_print').show();
			$('#sinngle_print').hide();
		}
    });
});
</script>