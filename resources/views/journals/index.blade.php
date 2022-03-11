@extends('layouts.app')
@section('title', __('account.journal'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('account.journal')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
            {!! Form::select('transaction_type', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' =>
            __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('account_id', __('account.accounts') . ':') !!}
            {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'account.journal_list')])

    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal"
            data-href="{{action('JournalController@create')}}" data-container=".add_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="journal_table">
            <thead>
                <tr>
                    <th>@lang('account.journal_no')</th>
                    <th>@lang('account.date')</th>
                    <th>@lang('account.account')</th>
                    <th>@lang('account.debit')</th>
                    <th>@lang('account.credit')</th>
                    <th>@lang('account.note')</th>
                    <th>@lang('account.added_by')</th>
                    <th>@lang('account.action')</th>
                </tr>
            </thead>

        </table>
    </div>
    <div class="modal fade add_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    @endcomponent
</section>
@endsection

@if(!$account_access)
<style>
  .dataTables_empty{
        color: {{App\System::getProperty('not_enalbed_module_user_color')}};
        font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;
    }
</style>
@endif

@section('javascript')
<script>
    //Date range as a button
     $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            journal_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        journal_table.ajax.reload();
    });

  
    //employee list
    journal_table = $('#journal_table').DataTable({
        language: {
            "emptyTable": "@if(!$account_access) {{App\System::getProperty('not_enalbed_module_user_message')}} @else @lang('account.no_data_available_in_table') @endif"
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("JournalController@index")}}',
            data: function (d) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                    d.location_id = $('#location_id').val();
                    d.account_id = $('#account_id').val();
                }
            }
        },
        columnDefs: [
            {
                targets: 7,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'journal_id', name: 'journal_id' },
            { data: 'date', name: 'date' },
            { data: 'account_name', name: 'accounts.name' },
            { data: 'debit_amount', name: 'debit_amount' },
            { data: 'credit_amount', name: 'credit_amount' },
            { data: 'note', name: 'note' },
            { data: 'user', name: 'users.username' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    
    $('#location_id, #account_id').change(function(){
        journal_table.ajax.reload();
    })

    $(document).on('click', 'a.delete_journal', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This template will be deleted.',
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
                        if (result.success == 1) {
                            toastr.success(result.msg);
                            journal_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $(document).on('click', '.journal_edit', function(e) {
        e.preventDefault();
        $('div.edit_modal').load($(this).attr('href'), function() {
            $(this).modal('show');
        });
    });

    $('.add_modal').on('hidden.bs.modal', function () {
        $('.journal_rows').remove();
        console.log('asdf');
        
    })
</script>
@endsection