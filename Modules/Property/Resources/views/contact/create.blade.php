<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
    $form_id = 'quick_add_contact';
    }
    $custom_labels = !empty(session('business.custom_labels')) ? json_decode(session('business.custom_labels'), true) :
    [];
    $contact_fields = !empty(session('business.contact_fields')) ? session('business.contact_fields') : [];
    @endphp
    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\ContactController@store'), 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
      <div class="row">

        <div class="col-md-6 contact_type_div">
          <div class="form-group">
            {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('type', $types, !empty($type) ? $type : null , ['class' => 'form-control', 'id' =>
              'contact_type','placeholder'
              => __('messages.please_select'), 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-6   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('name', null, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']);
              !!}
            </div>
          </div>
        </div>



        <div class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
              </span>
              {!! Form::text('contact_id', $contact_id, ['class' => 'form-control','placeholder' =>
              __('lang_v1.contact_id'), 'readonly']); !!}
            </div>
          </div>
        </div>

        <div
          class="col-md-4  @if($type=='customer' && !array_key_exists('property_customer_tax_number', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_tax_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('tax_number', __('lang_v1.tax_nic_passport_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.tax_nic_passport_number')]);
              !!}
            </div>
          </div>
        </div>




        <div
          class="col-md-4 customer_fields  @if($type=='customer' && !array_key_exists('property_customer_customer_group', $contact_fields)) hide backend_hide @endif">
          <div class="form-group">
            {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-4 supplier_fields  @if($type=='supplier' && !array_key_exists('supplier_supplier_group', $contact_fields)) hide backend_hide @endif">
          <div class="form-group">
            {!! Form::label('supplier_group_id', __('lang_v1.supplier_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('supplier_group_id', $supplier_groups, '', ['class' => 'form-control']); !!}
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
            <p class="help-block">At least 6 character.</p>
          </div>
        </div>
        <div
          class="col-md-4 customer_fields   ">
          <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>,
              </span>
              {!! Form::password('confirm_password', ['class' => 'form-control', 'id' => 'confirm_password',
              'placeholder' => __('business.confirm_password')]); !!}
            </div>
            <p class="help-block">At least 6 character.</p>
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
        <div class="col-md-4 ">
          <div class="form-group">
            NIC Number:
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>
              </span>
              {!! Form::text('nic_number', null, ['class' => 'form-control ', 'id' =>
              'nic_number']); !!}
            </div>

          </div>
        </div>
        <div class="col-md-12">
          <hr />
        </div>
        <div
          class="col-md-3 @if($type=='supplier' && !array_key_exists('supplier_email', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::email('email', null, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3 @if($type=='supplier' && !array_key_exists('supplier_mobile', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
              </span>
              {!! Form::text('mobile', null, ['class' => 'form-control input_number', 'required', 'placeholder' =>
              __('contact.mobile')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3   @if($type=='supplier' && !array_key_exists('supplier_alternate_contact_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('alternate_number', null, ['class' => 'form-control input_number', 'placeholder' =>
              __('contact.alternate_contact_number')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3   @if($type=='supplier' && !array_key_exists('supplier_landline', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landline', __('contact.landline') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('landline', null, ['class' => 'form-control input_number', 'placeholder' =>
              __('contact.landline')]);
              !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-12   @if($type=='supplier' && !array_key_exists('supplier_address', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('address', __('lang_v1.address') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-home"></i>
              </span>
              {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' =>
              __('lang_v1.address')]);
              !!}
            </div>
          </div>
        </div>

        <div
          class="col-md-3   @if($type=='supplier' && !array_key_exists('supplier_city', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('city', __('business.city') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('business.city')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3   @if($type=='supplier' && !array_key_exists('supplier_state', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('state', __('business.state') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('business.state')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($type=='supplier' && !array_key_exists('supplier_country', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('country', __('business.country') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-globe"></i>
              </span>
              {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('business.country')]); !!}
            </div>
          </div>
        </div>
        <div
          class="col-md-3  @if($type=='customer' && !array_key_exists('property_customer_landmark', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_landmark', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('landmark', null, ['class' => 'form-control',
              'placeholder' => __('business.landmark')]); !!}
            </div>
          </div>
        </div>
        <div>

          <div class="col-md-12">
            <hr />
          </div>
          <div
            class="col-md-3  @if($type=='customer' && !array_key_exists('property_customer_custom_field_1', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_1', $contact_fields)) hide @endif">
            <div class="form-group">
              {!! Form::label('custom_field1', !empty($custom_labels['custom_field']['custom_field_1']) ?
              $custom_labels['custom_field']['custom_field_1'] : 'Customer Field 1' . ':') !!}
              {!! Form::text('custom_field1', null, ['class' => 'form-control',
              'placeholder' => !empty($custom_labels['custom_field']['custom_field_1']) ?
              $custom_labels['custom_field']['custom_field_1'] : 'Customer Field 1']); !!}
            </div>
          </div>
          <div
            class="col-md-3  @if($type=='customer' && !array_key_exists('property_customer_custom_field_2', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_2', $contact_fields)) hide @endif">
            <div class="form-group">
              {!! Form::label('custom_field2', !empty($custom_labels['custom_field']['custom_field_2']) ?
              $custom_labels['custom_field']['custom_field_2'] : 'Customer Field 2' . ':') !!}
              {!! Form::text('custom_field2', null, ['class' => 'form-control',
              'placeholder' =>!empty($custom_labels['custom_field']['custom_field_2']) ?
              $custom_labels['custom_field']['custom_field_2'] : 'Customer Field 2']); !!}
            </div>
          </div>
          <div
            class="col-md-3  @if($type=='customer' && !array_key_exists('property_customer_custom_field_3', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_3', $contact_fields)) hide @endif">
            <div class="form-group">
              {!! Form::label('custom_field3', !empty($custom_labels['custom_field']['custom_field_3']) ?
              $custom_labels['custom_field']['custom_field_3'] : 'Customer Field 3' . ':') !!}
              {!! Form::text('custom_field3', null, ['class' => 'form-control',
              'placeholder' => !empty($custom_labels['custom_field']['custom_field_3']) ?
              $custom_labels['custom_field']['custom_field_3'] : 'Customer Field 3']); !!}
            </div>
          </div>
          <div   class="col-md-3  @if($type=='customer' && !array_key_exists('property_customer_custom_field_4', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_4', $contact_fields)) hide @endif">
            <div class="form-group">
              {!! Form::label('custom_field4', !empty($custom_labels['custom_field']['custom_field_4']) ?
              $custom_labels['custom_field']['custom_field_4'] : 'Customer Field 4' . ':') !!}
              {!! Form::text('custom_field4', null, ['class' => 'form-control',
              'placeholder' => !empty($custom_labels['custom_field']['custom_field_4']) ?
              $custom_labels['custom_field']['custom_field_4'] : 'Customer Field 4']); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
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

      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
  $('#transaction_date_contact').datepicker({
      autoclose: true,
      format: datepicker_date_format,
  });
</script>