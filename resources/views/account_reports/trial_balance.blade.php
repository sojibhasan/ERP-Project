@extends('layouts.app')
@section('title', __( 'account.trial_balance' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.trial_balance')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-sm-12">
            <div class="col-sm-3 col-xs-6 pull-left">
                <label for="business_location">@lang('account.business_locations'):</label>
                {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'business_location']) !!}
            </div>
            <div class="col-sm-6 col-xs-6 text-center">
                <h3>{{request()->session()->get('business.name')}}</h3>
                <div class="clearfix"></div>
                <h5 style="margin:0px;" id="date_show"></h5>
            </div>
            <div class="col-sm-3 col-xs-6 pull-right">
                <label for="date_range">@lang('messages.filter_by_date'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="date_range" value="{{@format_date('now')}}" class="form-control" readonly>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="box box-solid">
        <div class="box-header print_section">
            <h3 class="box-title">{{session()->get('business.name')}} - @lang( 'account.trial_balance') - <span
                    id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered" id="trial_balance_table">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang('account.account_number')</th>
                        <th>@lang('account.account_name')</th>
                        <th>@lang('account.debit')</th>
                        <th>@lang('account.credit')</th>
                    </tr>
                </thead>
                @if($account_access)
                <tbody>
                 
                </tbody>
                @endif
                <tbody id="account_balances_details">
                    @if(!$account_access)
                    <tr class="text-center"
                        style="color: {{App\System::getProperty('not_enalbed_module_user_color')}}; font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;">
                        <td colspan="3"> {{App\System::getProperty('not_enalbed_module_user_message')}}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-gray">
                        <th colspan="2" class="text-right">@lang('sale.total')</th>
                        <td>
                            <span class="remote-data display_currency " data-currency_symbol="true" id="total_debit">
                                @if($account_access)
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="remote-data display_currency" data-currency_symbol="true" id="total_credit">
                                @if($account_access)
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                @endif
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    //Date picker
        if ($('#date_range').length == 1) {
            $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
    $(document).ready( function(){
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
        ];

        $('#date_range, #business_location').change( function() {
            trial_balance_table.ajax.reload();
            var d = new Date($(this).val());
            let date = $('#date_range').val().split(' - ');
            $('#date_show').text(date[0]+' To '+ date[1]);
            $('#hidden_date').text($(this).val());
        });

        let date = $('#date_range').val().split(' - ');
        $('#date_show').text(date[0]+' To '+ date[1]);
    });

    @if($account_access)
    $(document).ready( function(){
        // trial_balance_table
        trial_balance_table = $('#trial_balance_table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "{{action('AccountReportsController@trialBalance')}}",
                data : function (d) {
                    d.start_date = $('input#date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.location_id = $('select#business_location').val();
                }
            },
            @include('layouts.partials.datatable_export_button')
            columnDefs:[{
                    "targets": 3,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'account_number', name: 'A.account_number'},
                {data: 'name', name: 'A.name'},
                {data: 'debit', name: 'debit'},
                {data: 'credit', name: 'credit'}
            ],
            "fnDrawCallback": function (oSettings) {
                $('#total_debit').text(sum_table_col($('#trial_balance_table'), 'debit'));
                $('#total_credit').text(sum_table_col($('#trial_balance_table'), 'credit'));

                __currency_convert_recursively($('#trial_balance_table'));
            }
        });
    });

    @endif

    function writeDebit(account_list){
        for (var key in account_list) {
            if(parseInt(account_list[key]['debit_balance']) !== 0){
                var accnt_bal = __currency_trans_from_en(account_list[key]['debit_balance']);
                var accnt_bal_with_sym = __currency_trans_from_en(account_list[key]['debit_balance'], true);
                var account_tr = '<tr><td class="pl-20-td">' + account_list[key]['name'] + ' <b>' + account_list[key]['account_number']  + '</b></td><td><input type="hidden" class="debit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
                $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
            }
        }
    }
    function writeCredit(account_list){
        for (var key in account_list) {
            if(parseInt(account_list[key]['credit_balance']) !== 0){
                var accnt_bal = __currency_trans_from_en(account_list[key]['credit_balance']);
                var accnt_bal_with_sym = __currency_trans_from_en(account_list[key]['credit_balance'], true);
                var account_tr = '<tr><td class="pl-20-td">' + account_list[key]['name'] + ' <b>' + account_list[key]['account_number']  + '</b></td><td>&nbsp;</td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';
                $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
                
            }
        }
    }
</script>

@endsection