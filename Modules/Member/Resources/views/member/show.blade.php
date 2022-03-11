@extends('layouts.app')
@section('title', __('member::lang.view_member'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('member::lang.view_member') }}</h1>
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
                @include('member::member.partials.member_basic_info')
            </div>
            <div class="info_col">
                @include('member::member.partials.member_more_info')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('member_id', $member_dropdown, $member->id , ['class' => 'form-control select2', 'id' =>
            'member_id']); !!}

            <input type="hidden" id="sell_list_filter_customer_id" value="{{$member->id}}">
            <input type="hidden" id="purchase_list_filter_supplier_id" value="{{$member->id}}">
        </div>
        <div class="col-md-2 col-xs-12"></div>
        <div class="col-md-4 col-xs-12" style="margin-top: -14px;">
            @if($member->type == 'customer') <span class="text-red" style="font-size: 36px;"> @lang('member.customer'):
                {{$member->name}} </span> @endif
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                        @if(!empty($view_type) &&  $view_type == 'member_info')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#member_info_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                aria-hidden="true"></i> @lang( 'member::lang.member_info', ['member' =>
                            __('member.member') ])</a>
                    </li>

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
                            @if(!empty($view_type) &&  $view_type == 'member_info')
                                active
                            @else
                                ''
                            @endif" id="member_info_tab">
                        @include('member::member.member_info_tab')
                    </div>

                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif" id="documents_and_notes_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box'])
                                @include('member::member.documents_and_notes_tab')
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
<div class="modal fade pay_member_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script>
    $(document).ready( function(){
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
 $(document).ready( function(){
    getDocAndNoteIndexPage();
    setTimeout(() => {
        initializeDocumentAndNoteDataTable();
    }, 200);
});
    function getDocAndNoteIndexPage() {
    var notable_type = $('#notable_type').val();
    var notable_id = $('#notable_id').val();
    $.ajax({
        method: "GET",
        dataType: "html",
        url: "{{action('DocumentAndNoteController@getDocAndNoteIndexPage')}}",
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
            url: '/note-documents',
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
</script>
@endsection