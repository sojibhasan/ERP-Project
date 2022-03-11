<style>
    .tox-menubar{
        display: none !important;
    }
    .tox-toolbar-overlord{
        display: none !important;
    }
    .tox-statusbar{
        display: none !important;
    }
</style>
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
        <h4 class="modal-title">{{$suggestion->heading}}</h4>
      </div>
  
      <div class="modal-body">
        <div class="col-md-4">
            {!! Form::label('date', __( 'member::lang.date' )) !!} : {{$suggestion->date}}
        </div>
        <div class="col-md-4">
            {!! Form::label('balamandalaya', __( 'member::lang.balamandalaya' )) !!}: {{$suggestion->balamandalaya}}
        </div>
        <div class="col-md-4">
            {!! Form::label('service_areas', __( 'member::lang.service_areas' )) !!}: {{$suggestion->service_area}}
        </div>
  
        <div class="col-md-12">
            {!! Form::label('heading', __( 'member::lang.heading' )) !!}: {{$suggestion->heading}}
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('details', __( 'member::lang.details' )) !!}
                {!! Form::textarea('details', $suggestion->details, ['class' => 'form-control','placeholder' =>
                __('member::lang.details')]); !!}
              </div>
        </div>
  
        <div class="col-md-3">
            {!! Form::label('is_common_problem', __( 'member::lang.is_common_problem' )) !!}: {{ucfirst($suggestion->is_common_problem)}}
        </div>
        
        <div class="col-md-3">
            {!! Form::label('area_name', __( 'member::lang.area_name' )) !!}: {{$suggestion->area_name}}
        </div>
  
        <div class="col-md-3">
            {!! Form::label('state_of_urgency', __( 'member::lang.state_of_urgency' )) !!}: {{ucfirst($suggestion->state_of_urgency)}}
        </div>
        <div class="col-md-3">
            {!! Form::label('solution_given', __( 'member::lang.solution_given' )) !!}: {{ucfirst($suggestion->solution_given)}}
        </div>
        <div class="col-md-3">
            {!! Form::label('remarks', __( 'member::lang.remarks' )) !!}: {{ucfirst($suggestion->remarks)}}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
     if ($('#details').length) {
          tinymce.init({
              selector: 'textarea#details',
          });
      }
  </script>