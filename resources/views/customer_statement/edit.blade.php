<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('CustomerStatementController@update', [$transaction->id]), 'method' => 'put', 'id' => 'transaction_edit_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'sale.edit_discount' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="col-md-4">
                  <div class="form-group">
                    {!! Form::label('order_no', __( 'contact.order_no' ) . ':*') !!}
                      {!! Form::text('order_no', $transaction->order_no, ['class' => 'form-control',  'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                    {!! Form::label('order_date', __( 'contact.order_date' ) . ':*') !!}
                      {!! Form::text('order_date', !empty($transaction->order_date) ? \Carbon::parse($transaction->order_date)->format('m/d/Y') : null, ['class' => 'form-control', 'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                    {!! Form::label('customer_ref', __( 'contact.customer_reference' ) . ':*') !!}
                      {!! Form::select('customer_ref', $customer_references , $transaction->customer_ref, ['class' => 'form-control',  'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
              </div>
          </div>
          
  
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" id="transaction_edit_btn" class="btn btn-primary">@lang( 'messages.update' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

  <script>
      $('#order_date').datepicker();
  </script>