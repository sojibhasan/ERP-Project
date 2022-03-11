@extends('layouts.app')
@section('title', __('hr.employee_details'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.employee_details')</h1>
    <br>
    {{-- @include('layouts.partials.search_settings') --}}
</section>
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmployeeController@update', [$employee->id]), 'method' => 'put', 'id' => 'employee_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-md-12">
       <!--  <pos-tab-container> -->
        <div class="col-xs-12 pos-tab-container">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                <h5 style="color:#777; text-align: center; font-weight: bold;">{{$employee->first_name}} {{$employee->last_name}}</h5>
                <h6 style="color:dodgerblue; text-align: center; font-weight: bold; margin-bottom: 40px;">EMPLOYEE ID:  {{$employee->employee_id}}</h6>
                <div class="list-group">
                    <a href="#" class="list-group-item text-center active">@lang('hr::lang.details')</a>
                    <a href="#" class="list-group-item text-center">@lang('hr::lang.job')</a>
                    <a href="#" class="list-group-item text-center">@lang('hr::lang.salary')</a>
                    <a href="#" class="list-group-item text-center">@lang('hr::lang.login')</a>

                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
                @include('hr::employee.partials.details')
                <!-- tab 1 end -->
                <!-- tab 2 start -->
                @include('hr::employee.partials.job')
                <!-- tab 2 end -->
                <!-- tab 3 start -->
                @include('hr::employee.partials.salary')
                <!-- tab 3 end -->
                <!-- tab 4 start -->
                @include('hr::employee.partials.login')
                <!-- tab 4 end -->
            </div>
        </div>
        <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button" type="submit">@lang('hr::lang.update_employee')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{asset('js/editor.js')}}"></script>

    
<script>
	$("#tc_sale_and_pos_text").Editor();
    $("#tc_sale_and_pos_text").Editor("setText",'@if(!empty($pos_settings["tc_sale_and_pos_text"])){{$pos_settings["tc_sale_and_pos_text"]}} @endif');
    $('.settingForm_button').click(function(e){
        var editor_html1 = $("#tc_sale_and_pos_text").Editor("getText").replace('<p>', '').replace('</p>', '');
        $("#tc_sale_and_pos_text").val(editor_html1);
    });

    $('#datepicker1').datepicker();
    $('#datepicker2').datepicker();
    $('#datepicker3').datepicker();
</script>





<script>
    function show_monthly(){
        document.getElementById('hourly').style.display ='none';
        document.getElementById('monthly').style.display = 'block';
    }
    
    function show_hourly(){
        document.getElementById('hourly').style.display ='block';
        document.getElementById('monthly').style.display = 'none';
    }
    </script>
    <script>
    $(document).ready(function() {
        function calculate(valueType, salary, basicSalary ){
            if(valueType == 2 && salary != 0) {// deduction %
                var tmp  = salary / 100;
                return resultDeductionAmount = tmp * basicSalary;
    
            }else if(salary != 0){
                return resultDeductionAmount = salary;
            }else{
                return resultDeductionAmount = 0;
            }
        }
    
        //key press
        $(".key").keyup(function() {
            var allID = [];
            $('div input[name][id][value]').each(function(){
                allID.push($(this).attr('id'));
            });
    
            var totalPayable = 0 ;
            var totalCostCompany = 0;
            var totalPayableDeduction = 0;
            var totalCompanyDeduction = 0;
            var resultDeduction = 0;
    
            arrayLength = allID.length;
            for (var i = 0; i < arrayLength; i++) {
                var fieldId = allID[i].slice(4);
                var type = $( "#type"+fieldId ).val();
                var payable = $( "#pay"+fieldId ).val();
                var company = $( "#cost"+fieldId ).val();
                var flag = $( "#flag"+fieldId ).val();
                var valueType = $( "#valueType"+fieldId ).val();// amount or percentage
    
                var salary = ($.trim($("#earn" + fieldId).val()) != "" && !isNaN($("#earn" + fieldId).val())) ? parseInt($("#earn" + fieldId).val()) : 0;
                var basicSalary = ($.trim($("#earn1").val()) != "" && !isNaN($("#earn1").val())) ? parseInt($("#earn1").val()) : 0;
    
                if(flag==1){
                    $('#errorSalaryRange').empty();
                    var min_salary = parseInt($.trim($( "#min_salary" ).val()));
                    var max_salary = parseInt($.trim($( "#max_salary" ).val()));
                    if(salary < min_salary || salary > max_salary  ){
                        $("#errorSalaryRange").append('Salary Range: ' + min_salary + ' - ' + max_salary);
                    }
                }
    
                if(type == 1){
                    //total payable
                    if(payable == 1){
                        totalPayable  += calculate(valueType, salary, basicSalary );
                    }
                    //Cost to company
                    if(company == 1){
                        totalCostCompany += calculate(valueType, salary, basicSalary );
                    }
                }else{//Deduction
                    if(payable == 1 && company == 1 ){
                        var resultDeductionAmount = calculate(valueType, salary, basicSalary );
                        resultDeduction +=resultDeductionAmount ;
                    }else if(payable == 1){
                        var resultDeductionAmount = calculate(valueType, salary, basicSalary );
                        resultDeduction +=resultDeductionAmount ;
                    }else{
                        var resultDeductionAmount = calculate(valueType, salary, basicSalary );
                        resultDeduction +=resultDeductionAmount ;
                    }
    
                    if(payable == 1){//payable
                        var resultDeductionAmount = calculate(valueType, salary, basicSalary );
                        totalPayableDeduction +=resultDeductionAmount ;
                    }
    
                    if(company == 1){//cost to company
                        var resultDeductionAmount = calculate(valueType, salary, basicSalary );
                        totalCompanyDeduction +=resultDeductionAmount ;
                    }
                }
            }
    
            var myCurrency =  '<?php echo $currecy_symbol; ?>';
            // salary payable
            var resultSalaryPayable = totalPayable - totalPayableDeduction
            $('#resultTotalPayable').empty();
            $("#resultTotalPayable").append('<strong>'+ myCurrency +' '+ resultSalaryPayable  +'</strong>');
            $('#totalPayable').val(resultSalaryPayable);
    
            //salary cost to company
            var resultSalaryCostToCompany = totalCostCompany + totalCompanyDeduction;
            $('#resultCostToCompany').empty();
            $("#resultCostToCompany").append('<strong>'+ myCurrency +' '+ resultSalaryCostToCompany  +'</strong>');
            $('#totalCostCompany').val(resultSalaryCostToCompany);
    
            $('#resultTotalDeduction').empty();
            $("#resultTotalDeduction").append('<strong>'+ myCurrency +' '+ resultDeduction  +'</strong>');
            $('#totalDeduction').val(resultDeduction);
        });
    });
    
    $(document).ready(function() {
        var str = $("#salaryGrade").val();
        $('#resultSalaryRange').empty();
        var link = getBaseURL();
        $.ajax({
            type: "POST",
            url: link + 'admin/employee/get_salaryRange_by_id',
            data: { grade_id : str },
            cache: false,
            success: function(result){
                var result = $.parseJSON(result)
                $("#resultSalaryRange").append('Min: ' +result[0] + ' Max: ' + result[1]);
                $('#min_salary').val(result[0]);
                $('#max_salary').val(result[1]);
            }
        });
    });
    
    function get_Cookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        $('#token').val(cookieValue);
    }
    </script>


<script>
    @if(!empty($employee->joined_date))
    $('#joined_date').datepicker("setDate" , '{{$employee->joined_date}}');
    @endif
    @if(!empty($employee->date_of_permanency))
    $('#date_of_permanency').datepicker("setDate" , '{{$employee->date_of_permanency}}');
    @endif
    @if(!empty($employee->probation_end_date))
    $('#probation_end_date').datepicker("setDate" , '{{$employee->probation_end_date}}');
    @endif
    
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

    
@endsection