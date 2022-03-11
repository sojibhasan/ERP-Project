<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\TasksManagement\Http\Controllers\NoteGroupController@store'), 'method' => 'post', 'id' => 'account_type_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tasksmanagement::lang.add_note_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'tasksmanagement::lang.name' )) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'tasksmanagement::lang.name' ), 'id' => 'note_group_name']);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('color', __( 'tasksmanagement::lang.color' )) !!}
        {!! Form::text('color-picker', null, ['class' => 'form-control color-picker', 'required', 'id' => 'color-picker', 'placeholder' => __( 'tasksmanagement::lang.color' )]);
        !!}
        {!! Form::hidden('color', null, ['id' => 'color']) !!}
      </div>

      <div class="form-group">
        {!! Form::label('prefix', __( 'tasksmanagement::lang.prefix' )) !!}
        {!! Form::text('prefix', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'tasksmanagement::lang.prefix' )]);
        !!}
      </div>

     
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_note_group_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   const pickr = Pickr.create({
    el: '.color-picker',
    theme: 'classic', // or 'monolith', or 'nano'

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
</script>