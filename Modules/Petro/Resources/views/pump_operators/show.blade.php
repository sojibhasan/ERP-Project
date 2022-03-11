@extends('layouts.app')
@section('title', __('petro::lang.view_pump_operator'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('petro::lang.view_pump_operator') }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="hide print_table_part">
        <style type="text/css">
            .info_col {
                width: 25%;
                float: left;
                padding-left: 10px;
                padding-right: 10px;
            }

            .box {
                border: 0px !important;
            }
        </style>
        <div style="width: 100%;">
            <div class="info_col">
                @include('petro::pump_operators.contact_basic_info')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('pump_operator_id', $pump_operators, $pump_operator->id , ['class' => 'form-control select2', 'id' =>
            'pump_operator_id']); !!}
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                        @if(!empty($view_type) &&  $view_type == 'contact_info')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#contact_info_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                aria-hidden="true"></i> @lang( 'contact.contact_info', ['contact' =>
                            __('contact.contact') ])</a>
                    </li>

                    @if($pump_operator_ledger_permission)
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'ledger')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#ledger_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-anchor"
                                aria-hidden="true"></i> @lang('lang_v1.ledger')</a>
                    </li>
                    @endif
                
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif
                            ">
                        <a href="#documents_and_notes_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-paperclip" aria-hidden="true"></i>
                            @lang('lang_v1.documents_and_notes')</a>
                    </li>
                </ul>

                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'contact_info')
                                active
                            @else
                                ''
                            @endif" id="contact_info_tab">
                        @include('petro::pump_operators.partials.contact_info_tab')
                    </div>
                    @if($pump_operator_ledger_permission)
                    <div class="tab-pane
                                @if(!empty($view_type) &&  $view_type == 'ledger')
                                    active
                                @else
                                    ''
                                @endif" id="ledger_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('petro::pump_operators.partials.ledger_tab')
                            </div>
                        </div>
                    </div>
                    @endif
                   
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif" id="documents_and_notes_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box'])
                                @include('petro::pump_operators.partials.documents_and_notes_tab')
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    $('#ledger_date_range_new').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#ledger_date_range_new').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        }
    );
    $('#ledger_date_range_new').change( function(){
        get_contact_ledger();
    });
    get_contact_ledger();

});

$("input.transaction_types, input#show_payments").on('ifChanged', function (e) {
    get_contact_ledger();
});

function get_contact_ledger() {

    var start_date = '';
    var end_date = '';
    var transaction_types = $('input.transaction_types:checked').map(function(i, e) {return e.value}).toArray();
    var show_payments = $('input#show_payments').is(':checked');

    if($('#ledger_date_range_new').val()) {
        start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
    $.ajax({
        url: '/petro/pump-operators/ledger?pump_operator_id={{$pump_operator->id}}&start_date=' + start_date + '&transaction_types=' + transaction_types + '&show_payments=' + show_payments + '&end_date=' + end_date,
        dataType: 'html',
        success: function(result) {
            $('#contact_ledger_div')
                .html(result);
            __currency_convert_recursively($('#contact_ledger_div'));

            $('#ledger_table').DataTable({
                searching: false,
                ordering:false,
                paging:false,
                dom: 't'
            });
        },
    });
}

$(document).on('click', '#send_ledger', function() {
    var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var url = "{{action('NotificationController@getTemplate', [$pump_operator->id, 'send_ledger'])}}" + '?start_date=' + start_date + '&end_date=' + end_date;

    $.ajax({
        url: url,
        dataType: 'html',
        success: function(result) {
            $('.view_modal')
                .html(result)
                .modal('show');
        },
    });
})

$(document).on('click', '#print_ledger_pdf', function() {
    var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date;
    window.location = url;
});
</script>
{{-- @include('sale_pos.partials.sale_table_javascript') --}}
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>


<script type="text/javascript">
    getDocAndNoteIndexPage();
    setTimeout(() => {
        initializeDocumentAndNoteDataTable();
    }, 200);

    function getDocAndNoteIndexPage() {
    var notable_type = $('#notable_type').val();
    var notable_id = $('#notable_id').val();
    $.ajax({
        method: "GET",
        dataType: "html",
        url: "{{action('\Modules\Petro\Http\Controllers\PumperDocumentAndNoteController@getDocAndNoteIndexPage')}}",
        async: false,
        data: {'notable_type' : notable_type, 'notable_id' : notable_id},
        success: function(result){
            $('.document_note_body').html(result);
        }
    });
}

function initializeDocumentAndNoteDataTable() {
    documents_and_notes_data_table = $('#documents_and_notes_table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "{{action('\Modules\Petro\Http\Controllers\PumperDocumentAndNoteController@index')}}",
                data: function(d) {
                    d.notable_id = $('#notable_id').val();
                    d.notable_type = $('#notable_type').val();
                }
            },
            columnDefs: [
                {
                    targets: [0, 2, 4],
                    orderable: false,
                    searchable: false,
                },
            ],
            aaSorting: [[3, 'asc']],
            columns: [
                { data: 'action', name: 'action' },
                { data: 'heading', name: 'heading' },
                { data: 'createdBy'},
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
            ]
        });
}

</script>



<script>
    if ($('#ledger_date_range_new').length == 1) {
        $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
            $('#ledger_date_range_new span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
     
        });
        $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
            $('#ledger_date_range_new').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
      
    }
    //Date range as a button
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
    });

    $('#sell_list_filter_date_range, #sell_list_filter_payment_status').change(function(){
        sell_table.ajax.reload();
    });


    $(document).on('click', '#print_btn', function() {
        var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');

        var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date;


        $.ajax({
            method: 'get',
			url: url,
			data: {  },
			success: function(result) {
                $('#ledger_print').html(result);

                var divToPrint=document.getElementById('ledger_print');

                var newWin=window.open('','Print-Ledger');
            
                newWin.document.open();
            
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            
                newWin.document.close();

			},
		});
    });

    $('#pump_operator_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/petro/pump-operators')}}/" + $(this).val();
        }
    });
</script>
@endsection