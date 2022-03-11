@extends('layouts.employee')
@section('title', __('hr::lang.payment'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'hr::lang.payment' )
        <small>@lang( 'hr::lang.manage_payment' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('filter_month', __('hr::lang.month').':') !!}
                    {!! Form::text('filter_month', date('m-Y'), ['class' => 'form-control', 'id' => 'filter_month']);
                    !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'hr::lang.all_payment' )])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="payment_table">
            <thead>
                <tr>
                    <th>@lang( 'hr::lang.month' )</th>
                    <th>@lang( 'hr::lang.employee_number' )</th>
                    <th>@lang( 'hr::lang.employee_name' )</th>
                    <th>@lang( 'hr::lang.gross_hourly_salary' )</th>
                    <th>@lang( 'hr::lang.payment_amount' )</th>
                    <th>@lang( 'hr::lang.payment_method' )</th>
                    <th>@lang( 'hr::lang.type' )</th>
                    <th>@lang( 'messages.action' )</th>
                </tr>
            </thead>
        </table>
    </div>

    @endcomponent

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){   
          // payment_table
          payment_table = $('#payment_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:  '/employee/salaries',
                data : function(d){
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