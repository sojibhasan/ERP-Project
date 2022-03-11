<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-12" data-offset="0">
            <div class="wrap-fpanel">
              <div class="box" data-collapsed="0">
                <div class="box-header with-border bg-primary-dark">
                  <h3 class="box-title">@lang('hr::lang.edit_payment')</h3>
                </div>
                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\PayrollPaymentController@update', $payroll->id), 'method' => 'put', 'id' =>
                'payroll_form' ]) !!}
                <div class="panel-body">
                  <div class="panel_controls">
                    <div class="row">
                      <div class="well well-lg col-md-12">
                        @if($type == 'Monthly')
                        <h3>@lang('hr::lang.monthly_salary')</h3>
                        @else
                        <h3>@lang('hr::lang.hourly_salary')</h3>
                        <div style="background-color: #ffd2bc; padding: 15px">
                          @lang('hr::lang.hourly_payment_attention')
                        </div>
                        @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-4">
                          <label for="field-1" class="">@lang('hr::lang.month'): </label>
                          <span class="label label-success" style="font-size: 15px">{{$month}}</span>
                        </div>
                      </div>
                    </div>
                    @if($type == 'Hourly')
                    <div class="row">
                      <div class="col-md-12">
                        <label for="field-1" class="">@lang('hr::lang.date_range')
                          <span class="required" aria-required="true">*</span></label>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="date_range" class="form-control reservation"
                              value="@if(!empty($payroll)) {{$payroll->date_range}} @endif" required>
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label for="field-1" class="">@lang('hr::lang.employee'): </label>
                          {{$employee->first_name.' '.$employee->last_name}}
                        </div>
                        <div class="col-md-3">
                          <label class="" style="text-align: right">@lang('hr::lang.employee_number'): </label>
                          {{$employee->employee_number}}
                        </div>
                        <div class="col-md-3">
                          <label for="field-1" class="">@lang('hr::lang.department'): </label>
                          {{$department->department}}
                        </div>
                        <div class="col-md-3">
                          <label class="" style="text-align: right">@lang('hr::lang.job_title'): </label>
                          {{$employee->job_title}}
                        </div>
                      </div>
                    </div>
                    <br>
                    <br>

                    @if($type == 'Monthly')
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label for="field-1" class="">@lang('hr::lang.gross_salary'): </label>
                        </div>
                        <div class="col-md-3">
                          <input type="text" value="{{($salary->total_payable + $salary->total_deduction)}}"
                            class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                          <label class="" style="text-align: right">@lang('hr::lang.deduction'): </label>
                        </div>
                        <div class="col-md-2">
                          <input type="text" value="{{($salary->total_deduction)}}" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <br>
                    <br>
                    <!-- All Deduction -->
                    <div class="col-md-12">
                      <label for="field-1" class="">@lang('hr::lang.salary_all_deductions'): </label>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        @if(count($salaryDeductionList))
                        @foreach($salaryDeductionList as $deduction)
                        @php $salary = ''; @endphp
                        @if(!empty($empSalaryDetails))
                        @foreach ($empSalaryDetails as $key => $details)
                        @if ($deduction->id == $key)
                        @php $salary = $details; @endphp
                        @endif
                        @endforeach
                        @endif
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">{{$deduction->component_name}}
                              <strong>({{$deduction->value_type ==1 ? __('hr::lang.amount'): __('hr::lang.percentage') }})</strong></label>
                            <input type="text" name="{{$deduction->id}}" class="form-control key deduction"
                              id="{{'earn'.$deduction->id }}"
                              value="@if(!empty($deduction->component_amount)) {{$deduction->component_amount}} @endif">

                            <input type="hidden" value="{{$deduction->type}}" id="{{'type'.$deduction->id }}">
                            <input type="hidden" value="{{$deduction->total_payable}}" id="{{'pay'.$deduction->id }}">
                            <input type="hidden" value="{{$deduction->cost_company}}" id="{{'cost'.$deduction->id }}">
                            <input type="hidden" value="{{$deduction->flag}}" id="{{'flag'.$deduction->id }}">
                            <input type="hidden" value="{{$deduction->value_type}}"
                              id="{{'valueType'.$deduction->id }}">
                            <input type="hidden" name="deduction[]" value="{{$deduction->id }}">

                          </div>
                        </div>
                        @endforeach
                        @endif
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label class="">@lang('hr::lang.net_salary'): </label>
                          <input type="text" value=" {{ @number_format($salary->total_payable) }}" class="form-control"
                            disabled>
                        </div>
                      </div>
                    </div>
                    @else
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label for="field-1" class="">@lang('hr::lang.hourly_salary'): </label>
                          <input type="text" value=" {{ @number_format($salary->hourly_salary) }}" class="form-control"
                            disabled>
                        </div>
                        <div class="col-md-3">
                          <label class="" style="text-align: right">@lang('hr::lang.total_hour') <span class="required"
                              aria-required="true">*</span></label>
                          <input type="text" name="total_hour"
                            value=" @if(!empty($payroll->total_hour)){{$payroll->total_hour}} @endif"
                            class="form-control" id="total_hour" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label class="">@lang('hr::lang.net_salary'): </label>
                          <input type="text" id="total_net_salary"
                            value="@if(!empty($payroll->net_salary)) {{@number_format($payroll->net_salary)}} @endif"
                            class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    @endif

                    @if(!empty($award))
                    @php
                    $totalAward = 0;
                    @endphp
                    <div class="col-md-12">
                      <div class="row">
                        <label class="">@lang('hr::lang.award'): </label> <br>
                        @foreach($award as $item )
                        @if ($item->award_amount == '0.00') // skip even members
                        @continue
                        @endif

                        <div class="col-md-3">
                          <input type="text" value=" {{@number_format($item->award_amount)}}" class="form-control"
                            disabled>
                          <span style="font-size: small">{{$item->award_name}}</span>
                          @php $totalAward += $item->award_amount; @endphp

                        </div>
                        @endforeach
                      </div>
                    </div>
                    @endif

                    @php
                    $totalPayable = 0;
                    if($type == 'Monthly'){
                    if(!empty($totalAward)){
                    // $totalPayable = $totalAward + $salary->total_payable;
                    }else{
                    if(!empty($payroll->net_salary)){
                    // $totalPayable = $payroll->net_salary;
                    }else{
                    // $totalPayable = $salary->total_payable;
                    }
                    }
                    }else{
                    if(!empty($totalAward)){
                    $totalPayable = $totalAward + 0;
                    $award = $totalAward;
                    }else{
                    $totalPayable = 0;
                    }
                    }
                    @endphp
                    @if($type == 'Monthly')
                    <input type="hidden" value="{{$totalPayable}}" id="net_salary">
                    @else
                    <input type="hidden" value="{{$totalPayable}}" id="net_salary">
                    <input type="hidden" value="@if(!empty($award)) {{$award}}  @endif" id="award">
                    <input type="hidden" value="{{$salary->hourly_salary}}" id="hourly_salary">
                    @endif
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">@lang('hr::lang.fine_deduction'): </label>
                            <input type="text"
                              value="@if(!empty($payroll)) {{@number_format($payroll->fine_deduction)}} @endif"
                              class="form-control" name="fine_deduction" id="fine_deduction">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">@lang('hr::lang.bonus'): </label>
                            <input type="text" value="@if(!empty($payroll)) {{@number_format($payroll->bonus)}} @endif"
                              class="form-control" name="bonus" id="bonus">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">@lang('hr::lang.payment_amount'): </label>
                            <input type="text"
                              value="@if(!empty($payroll)) {{$payroll->net_payment}} @else {{$totalPayable}} @endif"
                              class="form-control" id="payment_amount" name="payment_amount" disabled>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Statutory Payment -->
                    <div class="col-md-12">
                      <label for="field-1" class="">@lang('hr::lang.statutory_payment_salary'): </label>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        @if(count($statutoryPaymentsList))
                        @foreach($statutoryPaymentsList as $statutory)
                        @php
                        $salary = '';
                        if(!empty($empSalaryDetails)) {
                        foreach ($empSalaryDetails as $key => $details) {
                        if ($statutory->id == $key) {
                        $salary = $details;
                        }
                        }
                        }
                        @endphp
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">{{$statutory->component_name}}
                              <strong>({{$statutory->value_type ==1 ? __('hr::lang.amount'): __('hr::lang.percentage') }})</strong></label>
                            <input type="text" name="{{$statutory->id }}" class="form-control key statutory"
                              id="{{'earn'.$statutory->id }}"
                              value="@if(!empty($deduction->component_amount)) {{$deduction->component_amount }} @endif">

                            <input type="hidden" value="{{$statutory->type}}" id="{{'type'.$statutory->id }}">
                            <input type="hidden" value="{{$statutory->total_payable}}" id="{{'pay'.$statutory->id }}">
                            <input type="hidden" value="{{$statutory->cost_company}}" id="{{'cost'.$statutory->id }}">
                            <input type="hidden" value="{{$statutory->flag}}" id="{{'flag'.$statutory->id }}">
                            <input type="hidden" value="{{$statutory->value_type}}"
                              id="{{'valueType'.$statutory->id }}">
                            <input type="hidden" name="statutory[]" value="{{$statutory->id }}">
                          </div>
                        </div>
                        @endforeach
                        @endif
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="">@lang('hr::lang.payment_method'): </label>
                            <select class="form-control select2" name="payment_method">
                              <option value="">@lang('hr::lang.please_select')...</option>
                              <option value="@lang('hr::lang.cash')" @if($payroll->payment_method == 'Cash') selected @endif>
                                @lang('hr::lang.cash')</option>
                              <option value="@lang('hr::lang.check')" @if($payroll->payment_method == 'Check') selected @endif>
                                @lang('hr::lang.check')</option>
                              <option value="@lang('hr::lang.electronic_transfer')"  @if($payroll->payment_method == 'Electronic Transfer') selected @endif>
                                @lang('hr::lang.electronic_transfer')</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label class="">@lang('hr::lang.comment'): </label>
                          <input type="text" value="@if(!empty($payroll)) {{$payroll->note}} @endif" name="note"
                            class="form-control">
                        </div>
                      </div>
                    </div>

                    <input type="hidden" name="employee_id" value="{{($employee->id)}} ">
                    <input type="hidden" name="month" value="{{($month)}} ">
                    <input type="hidden" name="payroll_id" value="@if(!empty($payroll->id)) {{($payroll->id)}} @endif">
                  </div>

                </div>

                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary" id="update_payroll_btn">@lang( 'messages.save' )</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
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
<script>
  $(function() {
$('#date').datepicker({
  autoclose: true,
  format: "yyyy-mm-dd",
});
});

$(function () {
$('.reservation').daterangepicker();
//Date range picker with time picker
});
</script>

@if($type == 'Monthly')
<script type="text/javascript">
  $(document).on("change", function() {
  var fine = 0;
  var bonus = 0;
  fine = $("#fine_deduction").val();
  bonus = $("#bonus").val();
  var net_salary = $("#net_salary").val();
  var total =  net_salary - fine + + bonus;
  $("#payment_amount").val(total);
});
</script>
@else
<script type="text/javascript">
  $(document).on("change", function() {
  var fine = 0;
  var bonus = 0;
  var total_hour = $("#total_hour").val();
  var hourly_salary = $("#hourly_salary").val();
  var award = $("#award").val();
  var total_net_salary = total_hour*hourly_salary + + award;

  $("#total_net_salary").val(total_net_salary);


  fine = $("#fine_deduction").val();
  bonus = $("#bonus").val();
  //var net_salary = $("#net_salary").val();
  var total =  total_net_salary - fine + + bonus;
  $("#payment_amount").val(total);
});
</script>
@endif