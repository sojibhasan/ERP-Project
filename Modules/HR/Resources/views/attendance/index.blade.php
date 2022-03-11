@extends('layouts.app')
@section('title', __('hr::lang.attendance'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['attendance'])
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#attendance" class="" data-toggle="tab">
                            <i class="fa fa-thumbs-up"></i> <strong>@lang('hr::lang.attendance')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['import_attendance'])
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#import" class="" data-toggle="tab">
                            <i class="fa fa-download"></i> <strong>@lang('hr::lang.import')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        @if($permissions['attendance'])
        <div class="tab-pane active" id="attendance">
            @include('hr::attendance.create')
        </div>
        @endif
        @if($permissions['import_attendance'])
        <div class="tab-pane " id="import">
            @include('hr::attendance.import')
        </div>
        @endif
     
    </div>

    <div class="modal fade attendance_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>

@endsection

@section('javascript')
<script>
   
    $('#date').datepicker("setDate", @if(!empty($date)) "{{date('m/d/Y', strtotime($date))}}" @else new Date() @endif);

    $('#time_only1').datetimepicker({
        datepicker:false,
        format:'H:i'
    });
    $('#time_only2').datetimepicker({
        datepicker:false,
        format:'H:i'
    });

    // $('#parent_present').on('ifChecked', function(){
        
    //     $('.child_present').iCheck('check');
    // });
    // $('#parent_present').on('ifUnChecked', function(){
    //     $('.child_present').iCheck('uncheck');
    // });
    // $('#parent_absent').on('ifChecked', function(){
    //     $('.child_absent').iCheck('check');
    // });
    // $('#parent_absent').on('ifUnChecked', function(){
    //     $('.child_absent').iCheck('uncheck');
    // });
    
$(document).on('change', '#parent_present', function(){
    if($(this).prop('checked') == true){
        $('.child_present').prop("checked", true);
        
    }else{
        $('.child_present').prop("checked", false);
    }
    allChecboxChange($(this).prop('checked'), 'check_in');
})
$(document).on('change', '#parent_absent', function(){
    if($(this).prop('checked') == true){
        $('.child_absent').prop("checked", true);
        
    }else{
        $('.child_absent').prop("checked", false);
    }
    allChecboxChange($(this).prop('checked'), 'l_category');
})

function allChecboxChange(check , className){
    if(check){
        $(document).find('.'+className).css('display', 'block');
    }else{
        $(document).find('.'+className).css('display', 'none');
    }
}


$('#sbtn').click(function(e){
    e.preventDefault();
    let href = $('#get_attendance_from').attr('action');

    $.ajax({
        method: 'get',
        url: href,
        data: { 
            department_id : $('#department_id').val(), date : $('#date').val()
         },
        dataType: 'html',
        success: function(result) {
            $('#set_attendance_div').empty().append(result);
        },
    });
})

$(document).on('change', '.child_present', function(){
    if($(this).prop('checked') == true){
        $(this).parent().find('#check_in').css('display', 'block');
    }else{
        $(this).parent().find('#check_in').css('display', 'none');
    }
})
$(document).on('change', '.child_absent', function(){
    if($(this).prop('checked') == true){
        $(this).parent().find('#l_category').css('display', 'block');
    }else{
        $(this).parent().find('#l_category').css('display', 'none');
    }
})


</script>

<script>
    $('#location_id').change(function () {
        employee_table.ajax.reload();
    });
    //employee list
    employee_table = $('#employee_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\EmployeeController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
                // d.contact_type = $('#contact_type').val();
            }
        },
        columns: [
            { data: 'employee_id', name: 'employee_id' },
            { data: 'employee_number', name: 'employee_number' },
            { data: 'name', name: 'name' },
            { data: 'department', name: 'department' },
            { data: 'title', name: 'title' },
            { data: 'employment_status', name: 'employment_status' },
            { data: 'work_shift', name: 'work_shift' },
           
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This employee will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            employee_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>
@endsection