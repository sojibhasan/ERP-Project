<div class="modal-dialog" role="document" style="width: 65%">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\LeadsController@update',
    $leads->id), 'method' => 'PUT', 'id' => 'leads_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'leads::lang.edit_leads' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('date', __( 'leads::lang.date' )) !!}
          {!! Form::text('date', \Carbon::parse($leads->date)->format('m/d/Y'), ['class' => 'form-control', 'required',
          'placeholder' => __(
          'leads::lang.date' ),
          'id' => 'leads_date']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('sector', __( 'leads::lang.sector' )) !!}
          {!! Form::select('sector', ['private' => __('leads::lang.private'), 'government' =>
          __('leads::lang.government')], $leads->sector, ['class' => 'form-control select2',
          'required', 'style' => 'width: 100%',
          'placeholder' => __(
          'leads::lang.please_select' ), 'id' => 'sector']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('category_id', __( 'leads::lang.category' )) !!}
          {!! Form::select('category_id', $categories, $leads->category_id, ['class' => 'form-control select2',
          'required', 'style' => 'width: 100%',
          'placeholder' => __(
          'leads::lang.please_select' ), 'id' => 'category_id']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('main_organization', __( 'leads::lang.main_organization' )) !!}
          {!! Form::text('main_organization', $leads->main_organization, ['class' => 'form-control', 'placeholder' =>
          __(
          'leads::lang.main_organization' ),
          'id' => 'main_organization']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('business', __( 'leads::lang.business' )) !!}
          {!! Form::text('business', $leads->business, ['class' => 'form-control', 'placeholder' => __(
          'leads::lang.business' ),
          'id' => 'business']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('address', __( 'leads::lang.address' )) !!}
          {!! Form::text('address', $leads->address, ['class' => 'form-control', 'placeholder' => __(
          'leads::lang.address' ),
          'id' => 'address']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('town', __( 'leads::lang.town' )) !!}
          {!! Form::text('town', $leads->town, ['class' => 'form-control', 'required','placeholder' => __(
          'leads::lang.town' ),
          'id' => 'town']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('district', __( 'leads::lang.district' )) !!}
          {!! Form::text('district', $leads->district, ['class' => 'form-control', 'required','placeholder' => __(
          'leads::lang.district' ),
          'id' => 'district']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('mobile_no_1', __( 'leads::lang.mobile_no_1' )) !!}
          {!! Form::text('mobile_no_1', $leads->mobile_no_1, ['class' => 'form-control input_number', 'required','placeholder' => __(
          'leads::lang.mobile_no_1' ),
          'id' => 'mobile_no_1']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('mobile_no_2', __( 'leads::lang.mobile_no_2' )) !!}
          {!! Form::text('mobile_no_2', $leads->mobile_no_2, ['class' => 'form-control input_number', 'placeholder' => __(
          'leads::lang.mobile_no_2' ),
          'id' => 'mobile_no_2']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('mobile_no_3', __( 'leads::lang.mobile_no_3' )) !!}
          {!! Form::text('mobile_no_3', $leads->mobile_no_3, ['class' => 'form-control input_number', 'placeholder' => __(
          'leads::lang.mobile_no_3' ),
          'id' => 'mobile_no_3']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('land_number', __( 'leads::lang.land_number' )) !!}
          {!! Form::text('land_number', $leads->land_number, ['class' => 'form-control input_number', 'placeholder' => __(
          'leads::lang.land_number' ),
          'id' => 'land_number']);
          !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_leads_btn">@lang( 'leads::lang.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#leads_date').datepicker({
        format: 'mm/dd/yyyy'
    });
    $('.select2').select2();
</script>