<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\PayrollPaymentController@store'), 'method' => 'post',
    'id' => 'basic_salary_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.add_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('department_id', __( 'hr::lang.department' ) . ':*') !!}
            {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'style' => 'width:
            100%;', 'required', 'placeholder' => __( 'hr::lang.please_select' ), 'id' => 'department_id']);
            !!}
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('employee_id', __( 'hr::lang.employee' ) . ':*') !!}
            {!! Form::select('employee_id', [], null, ['class' => 'form-control select2', 'style' => 'width: 100%;',
            'required', 'placeholder' => __( 'hr::lang.please_select' ), 'id' => 'employee_id']);
            !!}
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('month', __( 'hr::lang.month' ) . ':*') !!}
            {!! Form::text('month', date('m-Y'), ['class' => 'form-control', 'required', 'placeholder' => __(
            'hr::lang.month' ), 'id' => 'month']);
            !!}
          </div>
        </div>
      </div>
      <!-- make payment page contect append here -->
      <div id="add_payment_section"></div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="add_payment_modal_btn">@lang( 'messages.add' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('.select2').select2();
  $('#month').datepicker( {
      format: "mm-yyyy",
      viewMode: "months", 
      minViewMode: "months"
  });

  $('#department_id').change(function(){
    $.ajax({
      method: 'get',
      url: "{{action('\Modules\HR\Http\Controllers\BasicSalaryController@getEmployeeByDepartment')}}", 
      data: { department_id : $(this).val() },
      success: function(result) {
        var html = '<option value="" >Please Select</option>';
        result.employees.forEach(element => {
          html +='<option value="'+element["id"]+'">'+element["first_name"]+' '+element["last_name"]+'</option>';
        });
        $('#employee_id').empty().append(html);
      },
    });
  })

  $('#add_payment_modal_btn').click(function(){
    $.ajax({
      method: 'get',
      url: "{{action('\Modules\HR\Http\Controllers\PayrollPaymentController@getMakePayment')}}", 
      data: { 
        department_id : $('#department_id').val(),
        employee_id : $('#employee_id').val(),
        month : $('#month').val(),
       },
      success: function(result) {
        if(result.success == 0){
          toastr.error(result.msg);
        }else{
          $('#add_payment_section').append(result);
          $('.modal-footer').empty().append(`
            <button type="submit" class="btn btn-primary" id="save_payment_modal_btn">Add Payment</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          `);

        }
        
      },
    });

  })
</script>