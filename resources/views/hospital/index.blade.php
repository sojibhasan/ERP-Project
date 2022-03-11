@extends('layouts.app')
@section('title', __('patient.patient_details'))

@section('content')
@inject('request', 'Illuminate\Http\Request')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('patient.patient_details')</h1>
</section>


<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>Payee name:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <p id="vou_pyeename"></p>
    </div>
    <br/>
</div>
<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>Amount Number:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <p id="vou_payamnum"></p>
    </div>
</div>
<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>Amount in word:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <p id="vou_payamnumword"></p>
    </div>
    <br/>
</div>
<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>Chaque no.:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <div class="col-md-7" style="display:inline-block;">
            <p id="vou_chaqno"></p>
        </div>
    </div>
    <br/>
</div>
<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>Chaque Date:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <div class="col-md-7" >
            <p id="vou_date"><?php echo date('d-m-Y'); ?></p>
        </div>
    </div>
    <br/>
</div>
<div class="col-md-12" style="width: 100%;">
    <div class="col-md-3" style="display:inline-block;">
        <label><b>On Account of:</b></label>
    </div>
    <div class="col-md-7" style="display:inline-block;">
        <label id="vou_amountof" style="border-bottom: 1px dashed;"></label>
    </div>
    <br/>
</div>


<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    {!! Form::open(['url' => action('HospitalController@index') , 'method' => 'GET']) !!}
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('patient', __('patient.patient_code')) !!}
            {!! Form::text('patient_code', '', ['class' => 'form-control', 'placeholder' => __('patient.patient_details_info')
            ]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <button class="btn btn-success" style="margin-top: 24px;" type="submit">@lang('patient.filter')</button>
    </div>
    {!! Form::close() !!}
    @endcomponent
    @if ( $request->patient_code != '' && isset($patient_business_id))


    <div class="row">
        <div class="col-md-3" style="padding: 0px 10px;">
            @include('patient.partials.patient_sidebar')
        </div>
        <div class="col-md-9" style="padding-left: 0px;">
            @component('components.widget', ['class' => 'box-primary'])
            <div id="patient_tabs" style="min-height: 550px;">
                <div class="tab">
                    <button class="tablinks active"
                        onclick="patientTabs(event, 'madical_info')">@lang('patient.madical_info')</button>
                    <button class="tablinks"
                        onclick="patientTabs(event, 'prescriptions')">@lang('patient.prescriptions')</button>
                    <button class="tablinks" onclick="patientTabs(event, 'pharmacy')">@lang('patient.pharmacy')</button>
                    <button class="tablinks" onclick="patientTabs(event, 'tests')">@lang('patient.tests')</button>
                    <button class="tablinks" onclick="patientTabs(event, 'payments')">@lang('patient.payments')</button>
                    <button class="tablinks"
                        onclick="patientTabs(event, 'allergies_and_notes')">@lang('patient.allergies_and_notes')</button>
                </div>

                <!-- Tab content -->
                <div id="madical_info" class="tabcontent active">
                    @include('patient.partials.medical_info')
                </div>

                <div id="prescriptions" class="tabcontent">
                    @include('patient.partials.prescriptions')
                </div>

                <div id="pharmacy" class="tabcontent">
                    @include('patient.partials.pharmacy')
                </div>

                <div id="tests" class="tabcontent">
                    @include('patient.partials.tests')
                </div>

                <div id="payments" class="tabcontent">
                    @include('patient.partials.payments')
                </div>

                <div id="allergies_and_notes" class="tabcontent">
                    @include('patient.partials.allergies_and_notes')
                </div>
            </div>
            @endcomponent

        </div>
    </div>
    @endif

</section>
<!-- /.content -->

@endsection

@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script>
    function patientTabs(evt, cityName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

<script>
    var prescription_table = $('#prescription_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{action("PrescriptionController@index")}}',
        data: function(d) {
            d.patient_id = @if(isset($patient_business_id)){{$patient_business_id}}@endif; //this is patient user id, set in hospital controller
        }
    },
    columnDefs: [
        {
          @if (request()->session()->get('business.is_patient'))
            targets: 4,
          @else
          targets: 3,
          @endif
            orderable: false,
            searchable: false,
        },
    ],
    columns: [
        { data: 'date', name: 'date' },
        { data: 'hospital', name: 'hospital' },
        { data: 'doctor', name: 'doctor' },
        @if (request()->session()->get('business.is_patient'))
        { data: 'amount', name: 'amount' },
        @endif
        { data: 'action', name: 'action' },
    ],
});


</script>
<script>
    var test_table = $('#test_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{action("TestController@index")}}',
        data: function(d) {
            d.patient_id =  @if(isset($patient_business_id)){{$patient_business_id}}@endif;
        }
    },
    columnDefs: [
        {
          @if (request()->session()->get('business.is_patient'))
            targets: 4,
          @else
          targets: 3,
          @endif
            orderable: false,
            searchable: false,
        },
    ],
    columns: [
        { data: 'date', name: 'date' },
        { data: 'laboratory_name', name: 'laboratory_name' },
        { data: 'test_name', name: 'test_name' },
        @if (request()->session()->get('business.is_patient'))
        { data: 'amount', name: 'amount' },
        @endif
        { data: 'action', name: 'action' },
    ],
});

</script>
<script>
    var medicine_table = $('#medicine_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{action("MedicineController@index")}}',
        data: function(d) {
            d.patient_id =  @if(isset($patient_business_id)){{$patient_business_id}}@endif;
        }
    },
    columnDefs: [
        {
            targets: 5,
            orderable: false,
            searchable: false,
        },
    ],
    columns: [
        { data: 'date', name: 'date' },
        { data: 'pharmacy_name', name: 'pharmacy_name' },
        { data: 'medicine_name', name: 'medicine_name' },
        { data: 'qty', name: 'qty' },
        { data: 'notes', name: 'notes' },
        { data: 'action', name: 'action' },
    ],
});


</script>
<script>
    var allergie_table = $('#allergie_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{action("AllergiesController@index")}}',
        data: function(d) {
            d.patient_id =  @if(isset($patient_business_id)){{$patient_business_id}}@endif;
        }
    },
    columnDefs: [
        {
            targets: 0,
            width: '20%',
        },
        {
            targets: 1,
            orderable: false,
            searchable: false,
            width: '80%',
        },
    ],
    columns: [
        { data: 'date', name: 'date' },
        { data: 'allergy_name', name: 'allergy_name' },
    ],
});
</script>

<script>
            
    var payments_table = $('#payments_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: '{{action("PatientPaymentController@index")}}',
          data: function(d) {
              d.patient_id = @if(isset($id)){{$id}}@endif; //this is patient user id, set in hospital controller
              d.filter_type = $('#filter_type').val();
              d.start_date = $('input[name="date-filter"]:checked').data('start');
              d.end_date = $('input[name="date-filter"]:checked').data('end');
          }
      },
      columnDefs: [
          {
              targets: 3,
              orderable: false,
              searchable: false,
          },
      ],
      columns: [
          { data: 'date', name: 'date' },
          { data: 'institution_name', name: 'institution_name' },
          { data: 'amount', name: 'amount' },
          { data: 'action', name: 'action' },
      ],
      "fnDrawCallback": function (oSettings) {
        $('#total_payments').text(__number_f(sum_table_col($('#payments_table'), 'amount')));
      },
  });
  
  $('#filter_type').change(function(){
    payments_table.ajax.reload();
  });
  
  $(document).on('change', 'input[name="date-filter"]', function() {
    payments_table.ajax.reload();
  });
  </script>
@endsection