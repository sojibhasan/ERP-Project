@php
$contact_fields = !empty(session('business.contact_fields')) ? session('business.contact_fields') : [];
@endphp
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\ContactController@update', [$contact->id]), 'method' => 'POST',
    'id' =>'contact_edit_form','enctype'=>"multipart/form-data",'files' => true]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>

    <div class="modal-body">

      <div class="row">

        <div class="col-md-6 ">
          <div class="form-group">
            {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('type', $types, $contact->type, ['class' => 'form-control', 'id' =>
              'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-6 ">
          <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'),
              'required']); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
              </span>
              <input type="hidden" id="hidden_id" value="{{$contact->id}}">
              {!! Form::text('contact_id', $contact->contact_id, ['class' => 'form-control','placeholder' =>
              __('lang_v1.contact_id'), 'readonly']); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-4  @if($contact->type=='customer' && !array_key_exists('property_customer_tax_number', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_tax_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('tax_number', $contact->tax_number, ['class' => 'form-control', 'placeholder' =>
              __('contact.tax_no')]); !!}
            </div>
          </div>
        </div>
     
        <div class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('transaction_date', __('lang_v1.transaction_date') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('transaction_date', null, ['class' => 'form-control input_number', 'id' =>
              'transaction_date_contact']); !!}
            </div>

          </div>
        </div>

        <div
          class="col-md-4 customer_fields  @if($contact->type=='customer' && !array_key_exists('property_customer_customer_group', $contact_fields)) hide backend_hide @endif">
          <div class="form-group">
            {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('customer_group_id', $customer_groups, $contact->customer_group_id, ['class' =>
              'form-control']); !!}
            </div>
          </div>
        </div>

        <div
          class="col-md-4 supplier_fields  @if(request()->get('type')=='supplier' && !array_key_exists('supplier_supplier_group', $contact_fields)) hide backend_hide @endif">
          <div class="form-group">
            {!! Form::label('supplier_group_id', __('lang_v1.supplier_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('supplier_group_id', $supplier_groups, $contact->supplier_group_id, ['class' =>
              'form-control']); !!}
            </div>
          </div>
        </div>

        <div
          class="col-md-4 customer_fields ">
          <div class="form-group">
            {!! Form::label('password', __('business.password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>
              </span>

              {!! Form::password('password', ['class' => 'form-control', 'id' => 'password','placeholder' =>
              __('business.password')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-4 customer_fields  ">
          <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>,
              </span>
              {!! Form::password('confirm_password', ['class' => 'form-control', 'id' => 'confirm_password',
              'placeholder' => __('business.confirm_password')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <hr />
        </div>
        <div
          class="col-md-3   @if($contact->type=='supplier' && !array_key_exists('supplier_email', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::email('email', $contact->email, ['class' => 'form-control','placeholder' =>
              __('business.email')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3 @if($contact->type=='supplier' && !array_key_exists('supplier_mobile', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
              </span>
              {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control', 'required', 'placeholder' =>
              __('contact.mobile')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_alternate_contact_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('alternate_number', $contact->alternate_number, ['class' => 'form-control', 'placeholder'
              => __('contact.alternate_contact_number')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_landline', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landline', __('contact.landline') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('landline', $contact->landline, ['class' => 'form-control', 'placeholder' =>
              __('contact.landline')]); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>

        <div
          class="col-md-3 @if($contact->type=='supplier' && !array_key_exists('supplier_city', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('city', __('business.city') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('city', $contact->city, ['class' => 'form-control', 'placeholder' => __('business.city')]);
              !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3   @if($contact->type=='supplier' && !array_key_exists('supplier_state', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('state', __('business.state') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('state', $contact->state, ['class' => 'form-control', 'placeholder' =>
              __('business.state')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_country', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('country', __('business.country') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-globe"></i>
              </span>
              {!! Form::text('country', $contact->country, ['class' => 'form-control', 'placeholder' =>
              __('business.country')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='customer' && !array_key_exists('property_customer_landmark', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_landmark', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('landmark', $contact->landmark, ['class' => 'form-control', 'placeholder' =>
              __('business.landmark')]); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
          <hr />
        </div>
        <div
          class="col-md-3  @if($contact->type=='customer' && !array_key_exists('property_customer_custom_field_1', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_1', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field1', __('lang_v1.contact_custom_field1') . ':') !!}
            {!! Form::text('custom_field1', $contact->custom_field1, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field1')]); !!}
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='customer' && !array_key_exists('property_customer_custom_field_2', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_2', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field2', __('lang_v1.contact_custom_field2') . ':') !!}
            {!! Form::text('custom_field2', $contact->custom_field2, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field2')]); !!}
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='customer' && !array_key_exists('property_customer_custom_field_3', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_3', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field3', __('lang_v1.contact_custom_field3') . ':') !!}
            {!! Form::text('custom_field3', $contact->custom_field3, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field3')]); !!}
          </div>
        </div>
        <div
          class="col-md-3  @if($contact->type=='customer' && !array_key_exists('property_customer_custom_field_4', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_4', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field4', __('lang_v1.contact_custom_field4') . ':') !!}
            {!! Form::text('custom_field4', $contact->custom_field4, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field4')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        @if($contact->type=='customer')
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('image','Image:') !!}
            {!! Form::file('image', ['id' => 'image']); !!}
          </div>
        </div>

        <div  class="col-md-3 ">
          <div class="form-group">
            {!! Form::label('signature', 'Signature:') !!}
            {!! Form::file('signature', ['id' => 'signature']); !!}
          </div>
        </div>
        @endif

      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#transaction_date_contact').datepicker('setDate', "{{!empty($ob_transaction) ? \Carbon::parse($ob_transaction->transaction_date)->format('m-d-Y') : null }}" )

</script>