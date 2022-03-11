@extends('layouts.ecom_customer')
@section('title', __('lang_v1.'.$type.'s'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.'.$type.'s')
        <small>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.'.$type.'s') ])</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @php
    if($type == 'customer'){
    $colspan = 15;

    }else{
    $colspan = 16;
    }

    @endphp
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.all_your_contact', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="customer_contact_table">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('lang_v1.contact_id')</th>

                    <th>@lang('user.name')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('lang_v1.customer_group')</th>
                    <th>@lang('lang_v1.credit_limit')</th>
                    <th>@lang('contact.total_sale_due')</th>
                    <th>@lang('lang_v1.total_sell_return_due')</th>
                    <th>@lang('contact.pay_term')</th>
                    <th>@lang('account.opening_balance')</th>
                    <th>@lang('contact.tax_no')</th>
                    <th>@lang('business.email')</th>
                    <th>@lang('business.address')</th>
                    <th>@lang('lang_v1.added_on')</th>
                    @if($reward_enabled)
                    <th id="rp_col">{{session('business.rp_name')}}</th>
                    @endif
                    <th>
                        @lang('lang_v1.contact_custom_field1')
                    </th>
                    <th>
                        @lang('lang_v1.contact_custom_field2')
                    </th>
                    <th>
                        @lang('lang_v1.contact_custom_field3')
                    </th>
                    <th>
                        @lang('lang_v1.contact_custom_field4')
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="6">
                        <strong>
                            @lang('sale.total'):
                        </strong>
                    </td>
                    <td><span class="display_currency" id="footer_contact_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_contact_return_due"
                            data-currency_symbol="true"></span></td>
                    <td></td>
                    <td><span class="display_currency" id="footer_contact_opening_balance"
                            data-currency_symbol="true"></span></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        var columns = [
            { data: 'action', searchable: false, orderable: false },
            { data: 'contact_id', name: 'contact_id' },
            { data: 'name', name: 'name' },
            { data: 'mobile', name: 'mobile' },
            { data: 'customer_group', name: 'cg.name' },
            { data: 'credit_limit', name: 'credit_limit' },
            { data: 'due', searchable: false, orderable: false },
            { data: 'return_due', searchable: false, orderable: false },
            { data: 'pay_term', name: 'pay_term', searchable: false, orderable: false },
            { data: 'opening_balance', name: 'opening_balance', searchable: false },
            { data: 'tax_number', name: 'tax_number' },
            { data: 'email', name: 'email' },
            { data: 'address', name: 'address', orderable: false },
            { data: 'created_at', name: 'contacts.created_at' },
            { data: 'custom_field1', name: 'custom_field1' },
            { data: 'custom_field2', name: 'custom_field2' },
            { data: 'custom_field3', name: 'custom_field3' },
            { data: 'custom_field4', name: 'custom_field4' },
        ];
        if ($('#rp_col').length) {
            columns.push({ data: 'total_rp', name: 'total_rp' });
        }
        var customer_contact_table = $('#customer_contact_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/customer/details',
                data: function (d) {
                    d.type = $('#contact_type').val();
                },
            },
            aaSorting: [[1, 'desc']],
            columns: columns,
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file"></i> Export to CSV',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('notexport')
                                ? true
                                : false;
                        },
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('notexport')
                                ? true
                                : false;
                        },
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i> Column Visibility',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('notexport')
                                ? true
                                : false;
                        },
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('notexport')
                                ? true
                                : false;
                        },
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('notexport')
                                ? true
                                : false;
                        },
                    },
                },
            ],
            fnDrawCallback: function (oSettings) {
                var total_due = sum_table_col($('#customer_contact_table'), 'contact_due');
                $('#footer_contact_due').text(total_due);
                var total_return_due = sum_table_col($('#customer_contact_table'), 'return_due');
                $('#footer_contact_return_due').text(total_return_due);
                var total_opening_balance = sum_table_col($('#customer_contact_table'), 'ob');
                $('#footer_contact_opening_balance').text(total_opening_balance);
                __currency_convert_recursively($('#customer_contact_table'));
            },
        });
    })

    $(document).on('click', '#delete-selected', function(e){
        e.preventDefault();
        var selected_rows = getSelectedRows();

        if(selected_rows.length > 0){
        $('input#selected_rows').val(selected_rows);
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            $('form#mass_delete_form').submit();
            }
        });
        } else{
        $('input#selected_rows').val('');
        swal('@lang("lang_v1.no_row_selected")');
        }    
    });
    function getSelectedRows() {
        var selected_rows = [];
        var i = 0;
        $('.row-select:checked').each(function () {
            selected_rows[i++] = $(this).val();
        });

        return selected_rows; 
    }


</script>
@endsection