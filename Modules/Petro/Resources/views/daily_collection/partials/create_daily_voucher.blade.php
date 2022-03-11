<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyVoucherController@store'), 'method' =>
      'post', 'id' => 'daily_voucher_form' ])
      !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'petro::lang.add_daily_voucher' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' )) !!}
            {!! Form::text('transaction_date', null, ['class' => 'form-control', 'required', 'placeholder' =>
            __( 'petro::lang.transaction_date')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('daily_vouchers_no', __( 'petro::lang.daily_vouchers_no' )) !!}
            {!! Form::text('daily_vouchers_no', $daily_vouchers_no, ['class' => 'form-control', 'required', 'placeholder' =>
            __( 'petro::lang.daily_vouchers_no')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('location_id', __( 'petro::lang.location' )) !!}
            {!! Form::select('location_id', $busness_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('pump_id', __( 'petro::lang.pump' )) !!}
            {!! Form::select('pump_id', $pumps, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('operator_id', __( 'petro::lang.pump_operator' )) !!}
            {!! Form::select('operator_id', $pump_operators, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('customer_id', __( 'petro::lang.customer' )) !!}
            {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('current_outstanding', __( 'petro::lang.current_outstanding' )) !!}
            {!! Form::text('current_outstanding', null, ['class' => 'form-control input_number', 'required', 'placeholder' =>
            __( 'petro::lang.current_outstanding')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('outstanding_pending', __( 'petro::lang.outstanding_pending' )) !!}
            {!! Form::text('outstanding_pending', null, ['class' => 'form-control input_number', 'required', 'placeholder' =>
            __( 'petro::lang.outstanding_pending')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('voucher_order_number', __( 'petro::lang.voucher_order_number' )) !!}
            {!! Form::text('voucher_order_number', null, ['class' => 'form-control', 'required', 'placeholder' =>
            __( 'petro::lang.voucher_order_number')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('voucher_order_date', __( 'petro::lang.voucher_order_date' )) !!}
            {!! Form::text('voucher_order_date', null, ['class' => 'form-control', 'required', 'placeholder' =>
            __( 'petro::lang.voucher_order_date')]);
            !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('vehicle_no', __( 'petro::lang.reference' )) !!}
            {!! Form::select('vehicle_no', [], null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
            __( 'petro::lang.please_select')]);
            !!}
          </div>
        </div>
  
      <div class="clearfix"></div>
  
      <table class="table table-responsive" id="daily_voucher_add_table">
        <thead>
          <tr>
            <th>@lang('petro::lang.product')</th>
            <th>@lang('petro::lang.unit_price')</th>
            <th>@lang('petro::lang.qty')</th>
            <th>@lang('petro::lang.sub_total')</th>
            <th>@lang('petro::lang.action')</th>
          </tr>
        </thead>
        <tbody>
          <tr class="product_row">
            <td>
              {!! Form::select('daily_voucher[0][product_id]', $products, null, ['class' => 'form-control select2 product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
              __( 'petro::lang.please_select')]) !!}
            </td>
            <td>
              {!! Form::text('daily_voucher[0][unit_price]', 0, ['class' => 'form-control unit_price input_number', 'style' => 'width: 120px;', 'placeholder' => __('petro::lang.unit_price'), 'readonly']) !!}
            </td>
            <td>
              {!! Form::text('daily_voucher[0][qty]', 0, ['class' => 'form-control qty input_number', 'style' => 'width: 120px;', 'placeholder' => __('petro::lang.qty')]) !!}
            </td>
            <td>
              {!! Form::text('daily_voucher[0][sub_total]', 0, ['class' => 'form-control sub_total' input_number, 'style' => 'width: 120px;', 'placeholder' => __('petro::lang.sub_total')]) !!}
            </td>
            <td>
              <button type="button" class="btn btn-xs btn-primary add_row" style="margin-top: 6px;">+</button>
            </td>
          </tr>
          <input type="hidden" name="index" id="index" value="1">
  
        </tbody>
      </table>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="save_daily_voucher_btn">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
    $('#transaction_date').datepicker("setDate" , new Date());
    $('#voucher_order_date').datepicker("setDate" , new Date());
    $('.select2').select2();
  
    $('#customer_id').change(function(){
      let customer_id = $('#customer_id :selected').val();
      
      $.ajax({
        method: 'get',
        url: '/petro/issue-customer-bill/get-customer-reference/'+customer_id,
        data: {  },
        contentType: 'html',
        success: function(result) {
          $('#vehicle_no').empty().append(result);
          
        },
      });
      $.ajax({
        method: 'get',
        url: '/pos/get_customer_details',
        data: { customer_id : customer_id },
        success: function(result) {
          $('#current_outstanding').val(result.due_amount);
          $('#outstanding_pending').val(result.due_amount);
          
        },
      });
    })
    $(document).on('change', '.product_id', function(){
      let product_id = $(this).val();
      let this_row = $(this).parent().parent();
      let this_unit_input = $(this).parent().parent().find('input.unit_price');
      
      $.ajax({
        method: 'get',
        url: '/petro/issue-customer-bill/get-product-price/'+product_id,
        data: {  },
        success: function(result) {
          this_unit_input.val(result);
          calculate_total(this_row);
        },
      });
    });
  
    $(document).on('change', '.unit_price, .qty, .discount, .tax', function(){
      let this_row = $(this).parent().parent();
      calculate_total(this_row);
    });
  
  
  
    function calculate_total(this_row){
      let unit_price = parseFloat(this_row.find('.unit_price').val());
      let qty = parseFloat(this_row.find('.qty').val());
      let sub_total = this_row.find('.sub_total');
  
      let subtotal = (unit_price * qty) ;
      
      sub_total.val(subtotal);
  
    }
  
    $(document).on('click', '.add_row', function(){
      let index = parseInt($('#index').val());
      $.ajax({
        method: 'get',
        url: '/petro/daily-voucher/get-product-row',
        data: { index : index },
        success: function(result) {
          $('#index').val(index+1);
          $('#daily_voucher_add_table').append(result);
        },
      });
      
    })
  </script>