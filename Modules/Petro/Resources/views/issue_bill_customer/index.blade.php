@extends('layouts.app')
@section('title', __('petro::lang.issue_bill_customer'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'petro::lang.all_issue_bill_customer')])
            @can('issue_customer_bill.add')
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_issue_bill_customer_btn"
                    data-href="{{action('\Modules\Petro\Http\Controllers\IssueCustomerBillController@create')}}"
                    data-container=".issue_bill_customer_model">
                    <i class="fa fa-plus"></i> @lang( 'petro::lang.add' )</button>
            </div>
            @endslot
            @endcan
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="issue_bill_customer_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'petro::lang.date' )</th>
                                <th>@lang( 'petro::lang.customer_bill_no' )</th>
                                <th>@lang( 'petro::lang.bill_amount' )</th>
                                <th>@lang( 'petro::lang.pump' )</th>
                                <th>@lang( 'petro::lang.pump_operator' )</th>
                                <th>@lang( 'petro::lang.customer' )</th>
                                <th>@lang( 'petro::lang.vehicle_no' )</th>
                                <th>@lang( 'petro::lang.order_no' )</th>
                                <th>@lang( 'petro::lang.user' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade issue_bill_customer_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // issue_bill_customer_table
        issue_bill_customer_table = $('#issue_bill_customer_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Petro\Http\Controllers\IssueCustomerBillController@index')}}",
            },
            columnDefs:[{
                    "targets": 9,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'customer_bill_no', name: 'customer_bill_no'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'pump_name', name: 'pump_name'},
                {data: 'operator_name', name: 'pump_operators.name'},
                {data: 'customer_name', name: 'contacts.name'},
                {data: 'reference', name: 'reference'},
                {data: 'order_bill_no', name: 'order_bill_no'},
                {data: 'username', name: 'users.username'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-issue_bill_customer', function(){
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
                            issue_bill_customer_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', 'a.print_bill', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });


        });


        $(document).on('click', '#add_issue_bill_customer_btn', function(){
            $('.issue_bill_customer_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
</script>
@endsection