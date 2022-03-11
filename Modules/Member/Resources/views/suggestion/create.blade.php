<div class="modal-dialog" role="document" style="width: 65%">
  <div class="modal-content">

    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\SuggestionController@store'), 'method' =>
    'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.add_suggestion' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('date', __( 'member::lang.date' )) !!}
          {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __(
          'member::lang.date' ),
          'id' => 'suggestion_date']);
          !!}
        </div>
      </div>
      @if (Auth::guard('web')->check())
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member', __( 'member::lang.member' )) !!}
          {!! Form::select('member_id', $members, null, ['class' => 'form-control select2', 'required',
          'placeholder' => __(
          'member::lang.please_select' ), 'id' => 'member']);
          !!}
        </div>
      </div>
      @endif
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('balamandalaya', __( 'member::lang.balamandalaya' )) !!}
          {!! Form::select('balamandalaya_id', $balamandalayas, null, ['class' => 'form-control select2', 'required',
          'placeholder' => __(
          'member::lang.please_select' ), 'id' => 'balamandalaya']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('service_areas', __( 'member::lang.service_areas' )) !!}
          {!! Form::select('service_area_id', $service_areas, null, ['class' => 'form-control select2', 'required',
          'placeholder' => __(
          'member::lang.please_select' ), 'id' => 'service_areas']);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('heading', __( 'member::lang.heading' )) !!}
          {!! Form::text('heading', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'member::lang.heading' ),
          'id' => 'heading']);
          !!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('details', __( 'member::lang.details' )) !!}
          {!! Form::textarea('details', '', ['class' => 'form-control','placeholder' =>
          __('member::lang.details')]); !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('is_common_problem', __( 'member::lang.is_common_problem' )) !!}
          {!! Form::select('is_common_problem', ['yes' => 'Yes', 'no' => 'No'], null, ['class' => 'form-control
          select2',
          'required', 'placeholder' => __(
          'member::lang.please_select' ), 'id' => 'is_common_problem']);
          !!}
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('area_name', __( 'member::lang.area_name' )) !!}
          {!! Form::text('area_name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'member::lang.area_name' ),
          'id' => 'area_name']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('state_of_urgency', __( 'member::lang.state_of_urgency' )) !!}
          {!! Form::select('state_of_urgency', $state_of_urgencies, null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'member::lang.please_select' ), 'id' => 'state_of_urgency']);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('file', __( 'member::lang.upload_document' ) . ':') !!}
          {!! Form::file('upload_document', []); !!}
        </div>
      </div>

      @can('add_remarks')
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('remarks', __( 'member::lang.remarks' )) !!}
          {!! Form::text('remarks', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'member::lang.remarks' ),
          'id' => 'remarks']);
          !!}
        </div>
      </div>
      @endcan

    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_suggestion_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#suggestion_date').datepicker({
        format: 'mm/dd/yyyy'
    });
   $('.select2').select2();

   if ($('#details').length) {
        tinymce.init({
            selector: 'textarea#details',
        });
    }
</script>