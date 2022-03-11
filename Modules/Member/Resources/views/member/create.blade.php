<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('MemberRegisterController@store'), 'method' =>
    'post', 'id' => 'member_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_member' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('username', __('business.member_code') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('username', $member_username, ['class' => 'form-control','placeholder' =>
            __('business.member_code'),
            'required', 'readonly']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_name', __('business.name') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_name', null, ['class' => 'form-control','placeholder' =>
            __('business.name'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_address', __('business.address') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_address', null, ['class' => 'form-control','placeholder' =>
            __('business.address'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_town', __('business.town') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_town', null, ['class' => 'form-control','placeholder' =>
            __('business.town'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_district', __('business.district') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_district', null, ['class' => 'form-control','placeholder' =>
            __('business.district'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_mobile_number_1', __('business.mobile_number_1') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_mobile_number_1', null, ['class' => 'form-control','placeholder' =>
            __('business.mobile_number_1'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_mobile_number_2', __('business.mobile_number_2') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_mobile_number_2', null, ['class' => 'form-control','placeholder' =>
            __('business.mobile_number_2')
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_mobile_number_3', __('business.mobile_number_3') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_mobile_number_3', null, ['class' => 'form-control','placeholder' =>
            __('business.mobile_number_3')
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_land_number', __('business.land_number') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_land_number', null, ['class' => 'form-control','placeholder' =>
            __('business.land_number')
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_gender', __('business.gender') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('member_gender', ['male' => 'Male', 'female' => 'Female'],null, ['class' =>
            'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            'required']); !!}
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_date_of_birth', __('business.date_of_birth') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('member_date_of_birth', null, ['class' => 'form-control','placeholder' =>
            __('business.date_of_birth'), 'id' => 'date_of_birth'
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('gramasevaka_area', __('business.gramasevaka_area') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('gramasevaka_area', $gramasevaka_areas, null, ['class'
            => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('bala_mandalaya_area', __('business.bala_mandalaya_area') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, null,
            ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_group', __('business.member_group') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('member_group', $member_groups, null,
            ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            'required']); !!}
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_password', __('business.password') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-key"></i>
            </span>

            {!! Form::password('member_password', ['class' => 'form-control', 'id' => 'password', 'style' =>
            'margin: 0px;','placeholder'
            => __('business.password')]); !!}
          </div>
          <p class="help-block" style="color: #222;">At least 6 character.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_confirm_password', __('business.confirm_password') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-key"></i>,
            </span>
            {!! Form::password('member_confirm_password', ['class' => 'form-control', 'id' =>
            'confirm_password', 'style' => 'margin: 0px;', 'placeholder' =>
            __('business.confirm_password')]); !!}
          </div>
        </div>
      </div>

    </div>

    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_member_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#date_of_birth').datepicker({
        format: 'mm/dd/yyyy'
    });
</script>