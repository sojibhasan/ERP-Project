<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\BasicSalaryController@update', $basic_salary->id), 'method' => 'put', 'id' =>
    'account_group_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_basic_salary' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('salary_date', __( 'hr::lang.date' ) . ':*') !!}
        {!! Form::text('salary_date', $basic_salary->salary_date, ['class' => 'form-control', 'required', 'placeholder'
        => __(
        'hr::lang.salary_date' ), 'id' => 'salary_date']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('department_id', __( 'hr::lang.department' ) . ':*') !!}
        {!! Form::select('department_id', $departments,$basic_salary->department_id, ['class' => 'form-control select2',
        'style' => 'width:
        100%;', 'required', 'placeholder' => __( 'hr::lang.please_select' ), 'id' => 'department_id']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('employee_id', __( 'hr::lang.employee' ) . ':*') !!}
        {!! Form::select('employee_id', $employees,$basic_salary->employee_id, ['class' => 'form-control select2',
        'style' => 'width: 100%;',
        'required', 'placeholder' => __( 'hr::lang.please_select' ), 'id' => 'employee_id']);
        !!}
      </div>
      <div class="form-group">
        {!! Form::label('salary_amount', __( 'hr::lang.salary_amount' ) . ':*') !!}
        {!! Form::number('salary_amount', $basic_salary->salary_amount, ['class' => 'form-control', 'required',
        'placeholder' => __(
        'hr::lang.salary_amount' ), 'id' => 'salary_amount']);
        !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="update_account_group_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
  $('.select2').select2();
  $('#salary_date').datepicker();

  $('#department_id').change(function(){
    $.ajax({
      method: 'get',
      url: "{{action('\Modules\HR\Http\Controllers\BasicSalaryController@getEmployeeByDepartment')}}", 
      data: { department_id : $(this).val() },
      success: function(result) {
        var html = '<option >Please Select</option>';
        result.employees.forEach(element => {
          html +='<option '+element["id"]+'>'+element["first_name"]+' '+element["last_name"]+'</option>';
        });
        $('#employee_id').empty().append(html);
      },
    });
  })
</script>