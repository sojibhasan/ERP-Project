<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">

  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'property::lang.customer_details' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('coustomer_name', __( 'property::lang.coustomer_name' )) !!} : {{$contact->name}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('coustomer_code', __( 'property::lang.coustomer_code' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('address', __( 'property::lang.address' )) !!} : @if($contact->landmark)
                    {{ $contact->landmark }}
                @endif
            
                {{ ', ' . $contact->city }}
            
                @if($contact->state)
                    {{ ', ' . $contact->state }}
                @endif
                <br>
                @if($contact->country)
                    {{ $contact->country }}
                @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('nic_number', __( 'property::lang.nic_number' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('phone_number', __( 'property::lang.phone_number' )) !!} : {{$contact->mobile}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('land_amount', __( 'property::lang.land_amount' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('payment_type', __( 'property::lang.payment_type' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
  
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('installments', __( 'property::lang.installments' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('installment_amount', __( 'property::lang.installment_amount' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('due_installment_next_due_date', __( 'property::lang.due_installment_next_due_date' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('total_balance_amount', __( 'property::lang.total_balance_amount' )) !!} : {{$contact->contact_id}}
                </div>
            </div>
  
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="button" class="btn btn-default add_block_btn" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>

  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  