@extends('layouts.app')
@section('title', __('patient.test'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('patient.test')</h1>
</section>
<style>
  label {
    color: black !important;
  }
</style>
@php
$dateOfBirth = date('Y-m-d', strtotime($patient_details->date_of_birth));
$today = date("Y-m-d");
$diff = date_diff(date_create($dateOfBirth), date_create($today));
$age = $diff->format('%y');
@endphp
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-3">
      @include('patient.partials.patient_sidebar')
    </div>
    <div class="col-md-9">
      @component('components.widget', ['class' => 'box-primary'])
      @include('laboratory.partials.add_test_form')
      @endcomponent

    </div>
  </div>
  
  <!-- Modal -->
  <div class="modal fade" id="add_new_medicine_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 70%;">
      <div class="modal-content">
        <div class="modal-body">

          <h3 class="form-header">@lang('patient.allergy_register')</h3>
          {!! Form::open(['url' => action('MedicineController@store'), 'method' => 'post', 'id' =>
          'medicine_register_form','files' => true ]) !!}
          @include('patient.partials.register_form_medicine')

          <button type="button" class="btn btn-secondary pull-right"
            data-dismiss="modal">@lang(('messages.close'))</button>
          {!! Form::submit(__('messages.submit'), ['class' => 'btn btn-success pull-right', 'id' =>
          'add_medicine_submit']) !!}
          <div class="row"></div>
          {!! Form::close() !!}
        </div>

      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="add_new_hospital_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 70%;">
      <div class="modal-content">
        <div class="modal-body">

          <h3 class="form-header">@lang('patient.laboratory_register')</h3>
          {!! Form::open(['url' => route('laboratory_register'), 'method' => 'post', 'id' =>
          'hospital_register_form','files' => true ]) !!}
          @include('business.partials.register_form_hospital',['business' => 'Laboratory Name'])

          <button type="button" class="btn btn-secondary pull-right"
            data-dismiss="modal">@lang(('messages.close'))</button>
          {!! Form::submit(__('messages.submit'), ['class' => 'btn btn-success pull-right', 'id' =>
          'add_hospital_submit']) !!}
          <div class="row"></div>
          {!! Form::close() !!}
        </div>

      </div>
    </div>
  </div>


</section>
<!-- /.content -->

@endsection

@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script type="text/javascript">
  $(document).ready(function(){
      $('.select2_register').select2();
      $("form#hospital_register_form").validate({
          errorPlacement: function(error, element) {
              if(element.parent('.input-group').length) {
                  error.insertAfter(element.parent());
              } else {
                  error.insertAfter(element);
              }
          },
          rules: {
              name: "required",
              email: {
                  email: true,
                  remote: {
                      url: "/business/register/check-email",
                      type: "post",
                      data: {
                          email: function() {
                              return $( "#email" ).val();
                          }
                      }
                  }
              },
              password: {
                  required: true,
                  minlength: 5
              },
              confirm_password: {
                  equalTo: "#password"
              },
              username: {
                  required: true,
                  minlength: 4,
                  remote: {
                      url: "/business/register/check-username",
                      type: "post",
                      data: {
                          username: function() {
                              return $( "#username" ).val();
                          }
                      }
                  }
              }
          },
          messages: {
              name: LANG.specify_business_name,
              password: {
                  minlength: LANG.password_min_length,
              },
              confirm_password: {
                  equalTo: LANG.password_mismatch
              },
              username: {
                  remote: LANG.invalid_username
              },
              email: {
                  remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
              }
          }
          
      });

      $("#business_logo").fileinput({'showUpload':false, 'showPreview':false, 'browseLabel': LANG.file_browse_label, 'removeLabel': LANG.remove});
  });


</script>

<script>
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

        
  $('#add_new_hospital').click(function(){
    $('#add_new_hospital_modal').modal({backdrop: 'static', keyboard: false}) ;
  });
  $('#add_new_doctor').click(function(){
    $('#add_new_doctor_modal').modal({backdrop: 'static', keyboard: false}) ;
  });
  $('#add_new_allergies').click(function(){
    $('#add_new_allergies_modal').modal({backdrop: 'static', keyboard: false}) ;
  });
  $('#add_new_medicine').click(function(){
    $('#add_new_medicine_modal').modal({backdrop: 'static', keyboard: false}) ;
  });
  $('#add_new_test').click(function(){
    $('#add_new_test_modal').modal({backdrop: 'static', keyboard: false}) ;
  });

  $('#hospital_register_form').submit(    function(e){     

    e.preventDefault();
    var $form =  $('#hospital_register_form');

    // check if the input is valid
    if($form.valid()){

      $.ajax({
        url: $('#hospital_register_form').attr('action'),
        type: $('#hospital_register_form').attr('method'),
        data: $('#hospital_register_form').serialize(),
        success: function(result) {
          if (result.success == 1) {
              toastr.success(result.msg);
              hospitals = result.hospitals;
              $('#hospital_id').empty();
              hospitals.forEach(element => {
                $('#hospital_id').append(`<option value'`+element.id+`'>`+element.name+`</option>`);
              });
              $('#add_new_hospital_modal').modal('hide');
          } else {
              toastr.error(result.msg);
          }
            
        }   
  
      });

    }

});


  $('#medicine_register_form').submit(    function(e){     
    e.preventDefault();

      $.ajax({
        url: $('#medicine_register_form').attr('action'),
        type: $('#medicine_register_form').attr('method'),
        data: $('#medicine_register_form').serialize(),
        success: function(result) {
          if (result.success == 1) {
              toastr.success(result.msg);
              medicine = result.medicines;
              $('#medicine_id').empty();
              medicine.forEach(element => {
                $('#medicine_id').append(`<option value'`+element.id+`'>`+element.medicine_name+`</option>`);
              });
              $('#add_new_medicine_modal').modal('hide');
          } else {
              toastr.error(result.msg);
          }
            
        }   
  
      });
  });

  $('#test_register_form').submit(    function(e){     
    e.preventDefault();

      $.ajax({
        url: $('#test_register_form').attr('action'),
        type: $('#test_register_form').attr('method'),
        data: $('#test_register_form').serialize(),
        success: function(result) {
          if (result.success == 1) {
              toastr.success(result.msg);
              test = result.tests;
              $('#test_id').empty();
              test.forEach(element => {
                $('#test_id').append(`<option value'`+element.id+`'>`+element.test_name+`</option>`);
              });
              $('#add_new_test_modal').modal('hide');
          } else {
              toastr.error(result.msg);
          }
            
        }   
  
      });
  });
  
  $('#add_medicine_row').click(function (e) {
    e.preventDefault();
    row_index =  parseInt($('#medicine_row_index').val());
    row_index= row_index+1;
    $('#medicine_rows').append(`   <div class="medicine_row"><div class="col-md-3" style="padding-left: 0px;">
                    
                    {!! Form::text('medicine[`+row_index+`][medicine_name]', null, ['class' => 'form-control', 'placeholder' =>
                    __('patient.medicine_name')]) !!}
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('medicine[`+row_index+`][amount]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.amount'), 'id' => 'pharmacy_amount']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('medicine[`+row_index+`][qty]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.qty'), 'id' => 'pharmacy_amount']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                  <a class="delete_medicine_row" style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                </div></div><div class="clearfix"></div>`).draggable();

   
    $('#medicine_row_index').val(row_index);
  })

  $('#add_test_row').click(function (e) {
    e.preventDefault();
    row_index =  parseInt($('#test_row_index').val());
    row_index= row_index+1;
    $('#test_rows').append(`   <div class="test_row"><div class="col-md-4" style="padding-left: 0px;">
                    
                    {!! Form::text('test[`+row_index+`][test_name]', null, ['class' => 'form-control', 'placeholder' =>
                    __('patient.test_name')]) !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('test[`+row_index+`][amount]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.amount'), 'id' => 'pharmacy_amount']) !!}
                        </div>
                    </div>
                </div>
              
                <div class="col-md-2">
                  <a class="delete_test_row" style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                </div></div><div class="clearfix"></div>`).draggable();

   
    $('#test_row_index').val(row_index);
  })

$('a.delete_medicine_row').click(function(e){
  console.log('asdf');
  
  $(this).parent().parent().remove();
});
$('a.delete_test_row').click(function(e){
  console.log('asdf');
  
  $(this).parent().parent().remove();
});
</script>
@endsection