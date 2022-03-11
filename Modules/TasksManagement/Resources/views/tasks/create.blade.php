<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    <style>
      .select2-results__option[aria-selected="true"]{
        display: none;
      }
    </style>
    {!! Form::open(['url' => action('\Modules\TasksManagement\Http\Controllers\TaskController@store'), 'method' =>
    'post', 'id' => 'task_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tasksmanagement::lang.add_task' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('date_and_time', __( 'tasksmanagement::lang.date_and_time' )) !!}
          {!! Form::text('date_and_time', date('m/d/Y H:i:s'), ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'tasksmanagement::lang.date_and_time'), 'readonly']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('group_id', __( 'tasksmanagement::lang.task_group' )) !!}
          {!! Form::select('group_id', $task_groups, null, ['class' => 'form-control group_id', 'id' =>
          'group_id', 'placeholder' => __( 'tasksmanagement::lang.please_select' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('task_id', __( 'tasksmanagement::lang.task_id' )) !!}
          {!! Form::text('task_id', $task_id, ['class' => 'form-control', 'required', 'placeholder' => __(
          'tasksmanagement::lang.task_id' ), 'readonly']);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('task_heading', __( 'tasksmanagement::lang.task_heading' )) !!}
          {!! Form::text('task_heading', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'tasksmanagement::lang.task_heading' )]);
          !!}
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="col-xs-12">
        <div class="form-group">
          {!! Form::label('task_details', __('tasksmanagement::lang.task_details') . ':') !!}
          {!! Form::textarea('task_details', '', ['class' => 'form-control','placeholder' =>
          __('tasksmanagement::lang.task_details')]); !!}
        </div>
      </div>
      <div class="clearfix"></div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('priority_id', __( 'tasksmanagement::lang.priority' )) !!}
          {!! Form::select('priority_id', $priorities, null, ['class' => 'form-control',
          'required', 'placeholder' => __(
          'tasksmanagement::lang.please_select' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('status', __( 'tasksmanagement::lang.status' )) !!}
          {!! Form::select('status', $status_array, null, ['class' => 'form-control',
          'required', 'placeholder' => __(
          'tasksmanagement::lang.please_select' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('members', __( 'tasksmanagement::lang.members' )) !!}
          {!! Form::select('members[]', $users , null, ['class' => 'form-control select2', 'multiple', 'style'
          => 'width: 100%;']);
          !!}
        </div>
      </div>
      <div class="clearfix"></div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('start_date', __( 'tasksmanagement::lang.start_date' )) !!}
          {!! Form::text('start_date', null, ['class' => 'form-control', 'placeholder' => __(
          'tasksmanagement::lang.start_date' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('end_date', __( 'tasksmanagement::lang.end_date' )) !!}
          {!! Form::text('end_date', null, ['class' => 'form-control', 'placeholder' => __(
          'tasksmanagement::lang.end_date' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('estimated_hours', __( 'tasksmanagement::lang.estimated_hours' )) !!}
          {!! Form::text('estimated_hours', null, ['class' => 'form-control', 'placeholder' => __(
          'tasksmanagement::lang.estimated_hours' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('reminder', __( 'tasksmanagement::lang.reminder' )) !!}
          {!! Form::select('reminder[]', $reminders_array , null, ['class' => 'form-control select2', 'multiple', 'style'
          => 'width: 100%;']);
          !!}
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('color', __( 'tasksmanagement::lang.color' )) !!}
          {!! Form::text('color-picker', null, ['class' => 'form-control color-picker', 'id' =>
          'color-picker', 'placeholder' => __( 'tasksmanagement::lang.color' )]);
          !!}
          {!! Form::hidden('color', '#03C74D', ['id' => 'color']) !!}
        </div>
      </div>

      
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('task_footer', __( 'tasksmanagement::lang.task_footer' )) !!}
          {!! Form::text('task_footer', null, ['class' => 'form-control', 'placeholder' => __(
          'tasksmanagement::lang.task_footer' )]);
          !!}
        </div>
      </div>

    </div>

    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_task_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#start_date').datepicker();
  $('#end_date').datepicker();

  $('#group_id').change(function(){
    let group_id = $(this).val();
    $.ajax({
      method: 'get',
      url: "{{action('\Modules\TasksManagement\Http\Controllers\TaskController@getTaskId')}}",
      data: { group_id },
      success: function(result) {
        $('#task_id').val(result.task_id);
      },
    });
  });


  const pickr = Pickr.create({
    el: '.color-picker',
    theme: 'classic', // or 'monolith', or 'nano'
    default: '#03C74D',
    position: 'top-middle',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    components: {

        // Main components
        preview: true,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: true,
            input: true,
            clear: true,
            save: true,
            useAsButton: false,
        }
    }
  }).on('save', (color, instance) => {
      $('#color').val(color.toHEXA().toString());
  });

  if ($('#task_details').length) {
      tinymce.init({
          selector: 'textarea#task_details',
      });
  }

  $('.select2').select2();
</script>