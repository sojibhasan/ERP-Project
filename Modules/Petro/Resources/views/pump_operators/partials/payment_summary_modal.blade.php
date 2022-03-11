@if(!empty(Auth::user()->pump_operator_id))
<style>
    #payment_summary_modal .dt-buttons{
        display: none !important;
    }
</style>
@endif
<div class="modal-dialog" role="document" style="width: 55%" id="payment_summary_modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.payment_summary' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    @include('petro::pump_operators.partials.payment_summary')
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
    pump_operators_payment_summary_table = $('#pump_operators_payment_summary_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@index', ['only_pumper' => true])}}",
            data: function(d) {
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'time', name: 'time' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'payment_type', name: 'payment_type' },
            { data: 'amount', name: 'amount' }
        ],
        fnDrawCallback: function(oSettings) {
            var footer_payment_summary_amount = sum_table_col($('#pump_operators_payment_summary_table'), 'amount');
            $('#footer_payment_summary_amount').text(footer_payment_summary_amount);
          
            __currency_convert_recursively($('#pump_operators_payment_summary_table'));
        },
    });
    </script>