<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    @php
      $form_id = 'contact_edit_form';
      if(isset($quick_edit)){
      $form_id = 'quick_edit_contact';
      }
    @endphp
      {!! Form::open(['url' => action('CRMController@update', $crm->id), 'method' => 'put', 'id' => $form_id ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('contact.add_contact')</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
      
        <div class="clearfix"></div>
        <div class="col-md-4 supplier_fields">
          <div class="form-group">
              {!! Form::label('business_name', __('business.business_name') . ':*') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-briefcase"></i>
                  </span>
                  {!! Form::text('business_name', $crm->business_name, ['class' => 'form-control', 'required', 'placeholder' => __('business.business_name')]); !!}
              </div>
          </div>
        </div>
  
        <div class="col-md-4">
          <div class="form-group">
              {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-id-badge"></i>
                  </span>
                  {!! Form::text('contact_id', $crm->contact_id, ['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
              </div>
          </div>
        </div>
         

          
          <div class="col-md-4 customer_fields">
            <div class="form-group">
                {!! Form::label('crm_group_id', __('lang_v1.crm_group') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-users"></i>
                    </span>
                    {!! Form::select('crm_group_id', $crm_groups, $crm->crm_group, ['class' => 'form-control']); !!}
                </div>
            </div>
          </div>
 
        <div class="col-md-12">
          <hr/>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('email', __('business.email') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-envelope"></i>
                  </span>
                  {!! Form::email('email', $crm->email, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-mobile"></i>
                  </span>
                  {!! Form::text('mobile', $crm->mobile, ['class' => 'form-control', 'required input_number', 'placeholder' => __('contact.mobile')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-phone"></i>
                  </span>
                  {!! Form::text('alternate_number', $crm->alternate_number, ['class' => 'form-control input_number', 'placeholder' => __('contact.alternate_contact_number')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('landline', __('contact.landline') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-phone"></i>
                  </span>
                  {!! Form::text('landline', $crm->landline, ['class' => 'form-control input_number', 'placeholder' => __('contact.landline')]); !!}
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('city', __('business.city') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-map-marker"></i>
                  </span>
                  {!! Form::text('city', $crm->city, ['class' => 'form-control', 'placeholder' => __('business.city')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('district', __('lang_v1.district') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-map-marker"></i>
                  </span>
                  {!! Form::text('district', $crm->district, ['class' => 'form-control', 'placeholder' => __('business.state')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('country', __('business.country') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-globe"></i>
                  </span>
                  {!! Form::text('country', $crm->country, ['class' => 'form-control', 'placeholder' => __('business.country')]); !!}
              </div>
          </div>
        </div>
     
        <div> 
        <div class="clearfix"></div>
        <div class="col-md-12">
          <hr/>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('custom_field1', __('lang_v1.contact_custom_field1') . ':') !!}
              {!! Form::text('custom_field1', $crm->custom_field1, ['class' => 'form-control', 
                  'placeholder' => __('lang_v1.contact_custom_field1')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('custom_field2', __('lang_v1.contact_custom_field2') . ':') !!}
              {!! Form::text('custom_field2', $crm->custom_field2, ['class' => 'form-control', 
                  'placeholder' =>__('lang_v1.contact_custom_field2')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('custom_field3', __('lang_v1.contact_custom_field3') . ':') !!}
              {!! Form::text('custom_field3', $crm->custom_field3, ['class' => 'form-control', 
                  'placeholder' => __('lang_v1.contact_custom_field3')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('custom_field4', __('lang_v1.contact_custom_field4') . ':') !!}
              {!! Form::text('custom_field4', $crm->custom_field4, ['class' => 'form-control', 
                  'placeholder' => __('lang_v1.contact_custom_field4')]); !!}
          </div>
        </div>
        </div>
        <div class="clearfix"></div>
  
      </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
    
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->