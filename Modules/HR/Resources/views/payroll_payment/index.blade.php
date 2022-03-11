@extends('layouts.app')
@section('title', __('hr::lang.payment'))

@section('content')

<!-- Content Header (Page header) -->
{{-- <section class="content-header">
    <h1>@lang( 'hr::lang.payment' )
        <small>@lang( 'hr::lang.manage_payment' )</small>
    </h1>
</section> --}}
<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['basic_salary'])
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#basic_salary" class="" data-toggle="tab">
                            <i class="fa fa-money"></i> <strong>@lang('hr::lang.basic_salary')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['salary_details'])
                    <li class="">
                        <a style="font-size:13px;" href="#salary_details" data-toggle="tab">
                            <i class="fa fa-info-circle"></i> <strong>@lang('hr::lang.salary_details')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['payroll_payments'])
                    <li class="">
                        <a style="font-size:13px;" href="#payment" data-toggle="tab">
                            <i class="fa fa-money"></i> <strong>@lang('hr::lang.payment')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        @if($permissions['basic_salary'])
        <div class="tab-pane active" id="basic_salary">
            @include('hr::basic_salary.index')
        </div>
        @endif
        @if($permissions['salary_details'])
        <div class="tab-pane" id="salary_details">
            @include('hr::salary.create')
        </div>
        @endif
        @if($permissions['payroll_payments'])
        <div class="tab-pane" id="payment">
            @include('hr::payroll_payment.payment')
        </div>
        @endif

    </div>

    <div class="modal fade basic_salary_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function(){   
          // basic_salary_table
          basic_salary_table = $('#basic_salary_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/hr/payroll/basic-salary',
            columnDefs:[{
                    "targets": 9,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'salary_date', name: 'salary_date'},
                {data: 'employee_id', name: 'employee_id'},
                {data: 'employee_name', name: 'employees.first_name'},
                {data: 'employee_number', name: 'employee_number'},
                {data: 'department', name: 'departments.department'},
                {data: 'job_title', name: 'job_title'},
                {data: 'status_name', name: 'status_name'},
                {data: 'shift_name', name: 'shift_name'},
                {data: 'salary_amount', name: 'salary_amount'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
    })

    $(document).on('click', 'button.basic_salary_delete', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            basic_salary_table.ajax.reload();
                        },
                    });
                }
            });
        })
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
        function calculate(valueType, salary, basicSalary){
            if(valueType == 2 && salary != 0) {
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
    
            var myCurrency =  '{{@number_format('default_currency')}}';
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
<?php
    if(!empty($months)){
        $monthArr = array();
        foreach($months as $month){
            if($month->salary_month != ""){
                $monthArr[] = $month->salary_month;
            }
        }
        $dismonth = implode(",",$monthArr);
    }else{
        $dismonth = 0;
    }
    ?>
<script>
    $(function () {
        $(".years").datepicker( {
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });
        $(".months").datepicker( {
            format: "mm",
            disableMonths: '[<?php echo $dismonth;?>]',
            viewMode: "months",
            minViewMode: "months"
        });
    });
$(document).ready(function(){
});
    function get_basicSalary(str){
        $.ajax({
            type: "get",
            url: '/hr/payroll/salary/get-salary-range-by-employee-id/'+str,
            data: { },
            cache: false,
            success: function(result){
                console.log(result.basic_salary);
                
                $('.earningbasic').val(result.basic_salary);
                $('#emp_id').val(str);
                $('.key').trigger('keyup');
            }
        });
    }
</script>


<script>
    $(document).ready(function(){   
          // payment_table
          payment_table = $('#payment_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:  '/hr/payroll/payment',
                data : function(d){
                    d.employee_id = $('#filter_employee').val();
                    d.month = $('#filter_month').val();
                }
            },
            columnDefs:[{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'month', name: 'month'},
                {data: 'employee_number', name: 'employee_number'},
                {data: 'employee_name', name: 'employees.first_name'},
                {data: 'gross_salary', name: 'gross_salary'},
                {data: 'net_payment', name: 'net_payment'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'type', name: 'type'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
    })

    $(document).on('click', 'button.payment_delete', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            payment_table.ajax.reload();
                        },
                    });
                }
            });
        })

        $('#add_payment_btn').click(function(){
            $('.payment_modal').modal({backdrop:'static',keyboard:false});
        })

        $(document).on('click', '.payment_print', function(){
            href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                success: function(result) {
                    var w = window.open('', '_self');
                    $(w.document.body).html(result);
                    w.print();
                    w.close();
                    location.reload();
                },
            });
        });

        $('#filter_month').datepicker( {
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months"
        });

        $('#filter_month, #filter_employee').change(function(){
            payment_table.ajax.reload();
        });
</script>
@endsection