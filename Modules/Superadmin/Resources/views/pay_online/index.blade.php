@extends('layouts.app')
@section('title', __('superadmin::lang.pay_online_list'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('superadmin::lang.pay_online_list')
    </h1>
</section>

<!-- Main content -->
<section class="content">

    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('pay_online_no', __('superadmin::lang.pay_online_no') . ':') !!}
            {!! Form::select('pay_online_no', $pay_online_nos, null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('status', __('superadmin::lang.status') . ':') !!}
            {!! Form::select('status', $status, null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('name', __('superadmin::lang.name') . ':') !!}
            {!! Form::select('name', $names, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('currency', __('superadmin::lang.currency') . ':') !!}
            {!! Form::select('currency', $currencies, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('paid_via', __('superadmin::lang.paid_via') . ':') !!}
            {!! Form::select('paid_via', ['payhere' => 'Payhere', 'offline' => 'Offline'], null, ['class' =>
            'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    @endcomponent


    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_online_payments',
    ['contacts' =>
    __('superadmin::lang.') ])])
    @if(auth()->user()->can('superadmin') )
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pay_online_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('superadmin::lang.pay_online_no')</th>
                    <th>@lang('superadmin::lang.reference_no')</th>
                    <th>@lang('superadmin::lang.name')</th>
                    <th>@lang('superadmin::lang.notes')</th>
                    <th>@lang('superadmin::lang.amount')</th>
                    <th>@lang('superadmin::lang.currency')</th>
                    <th>@lang('superadmin::lang.payment_method')</th>
                    <th>@lang('superadmin::lang.status')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>
    @endif
    @endcomponent
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            journal_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        journal_table.ajax.reload();
    });


    var columns = [
            { data: 'date', name: 'date' },
            { data: 'pay_online_no', name: 'pay_online_no' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'name', name: 'name' },
            { data: 'note', name: 'note' },
            { data: 'amount', name: 'amount' },
            { data: 'currency', name: 'currency' },
            { data: 'paid_via', name: 'paid_via' },
            { data: 'status', name: 'status' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    $(document).ready(function(){
        var pay_online_table = $('#pay_online_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\PayOnlineController@index")}}',
                data: function (d) {
                    if($('#date_range').val()) {
                        var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                        d.name = $('#name').val();
                        d.pay_online_no = $('#pay_online_no').val();
                        d.status = $('#status').val();
                        d.paid_via = $('#paid_via').val();
                        d.currency = $('#currency').val();
                    }
                }
            },
            columns: columns,
            fnDrawCallback: function(oSettings) {
            
            },
        });
        // change_status button
        $(document).on('click', 'button.change_status', function(){
              $("div#statusModal").load($(this).data('href'), function(){
                  $(this).modal('show');
                  $("form#status_change_form").submit(function(e){
                      e.preventDefault();
                      var url = $(this).attr("action");
                      var data = $(this).serialize();
                      $.ajax({
                          method: "PUT",
                          dataType: "json",
                          data: data,
                          url: url,
                          success:function(result){
                              if( result.success == true){
                                  $("div#statusModal").modal('hide');
                                  toastr.success(result.msg);
                                  pay_online_table.ajax.reload();
                              }else{
                                  toastr.error(result.msg);
                              }
                          }
                      });
                  });
              });
          });

          $('#name, #pay_online_no, #status, #paid_via, #currency, #date_range').change(function(){
            pay_online_table.ajax.reload();
          })
    })


</script>
@endsection