<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\TasksManagement\Http\Controllers\PriorityController@store'), 'method' =>
    'post', 'id' => 'account_type_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tasksmanagement::lang.add_priority' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('date', __( 'tasksmanagement::lang.date' )) !!}
        {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'tasksmanagement::lang.date' ), 'id' => 'priority_date']);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('name', __( 'tasksmanagement::lang.name' )) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
        'tasksmanagement::lang.name' ), 'id' => 'priority_name']);
        !!}
      </div>
      <div class="clearfix"></div>
      
      <div class="col-md-12 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" id="add_priority_row">@lang('tasksmanagement::lang.add')</button>
      </div>
      <div class="clearfix"></div>

    </div>
    <div class="col-md-12">
      <div class="">
        <table class="table table-striped table-bordered" id="priority_table_add" style="width: 100%;">
          <thead>
            <tr>
              <th>@lang( 'tasksmanagement::lang.name' )</th>
              <th>@lang( 'tasksmanagement::lang.date' )</th>
              <th>@lang( 'messages.action' )</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        <div class="main_inputs"></div>
      </div>
    </div>
    <input type="hidden" name="index" value="0" id="index">
   

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_priority_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#priority_date').datepicker('setDate', new Date());

 $(document).on('click', '#add_priority_row',function(){
  let priority_name = $('#priority_name').val();
  let priority_date = $('#priority_date').val();
  let index = parseInt( $('#index').val());

  $('#priority_table_add tbody').append(`<tr>
      <td>${priority_name}</td>
      <td>${priority_date}</td>
      <td><button type="button" data-id="${index}" class="btn btn-xs btn-danger remove_row" >x</button></td>
    </tr>`
  );

   $('.main_inputs').append(
    ` <input type="hidden" id="name_${index}" name="priority[${index}][name]" value="${priority_name}" id="name">
    <input type="hidden" id="date_${index}" name="priority[${index}][date]" value="${priority_date}" id="date">`
   );

   $('#index').val(index+1);
   $('#priority_name').val('');
 });

 $(document).on('click', '.remove_row', function(){
   this_index = $(this).data('id');
   $(this).parent().parent().remove();

   $('#name_'+this_index).remove();
   $('#date_'+this_index).remove();

 });
</script>