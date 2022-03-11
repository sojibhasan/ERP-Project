@extends('layouts.app')
@section('title', __('patient.patient_details'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('patient.patient_details')</h1>
</section>

@php
$dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
$today = date("Y-m-d");
$diff = date_diff(date_create($dateOfBirth), date_create($today));
$age = $diff->format('%y');
@endphp
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-3" style="padding: 0px 10px;">
      @include('patient.partials.patient_sidebar')
    </div>
    <div class="col-md-9" style="padding-left: 0px;">
      @component('components.widget', ['class' => 'box-primary'])
        @include('patient.partials.patient_details_edit')
      @endcomponent

    </div>
  </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp

<script>
    if (result.success == true) {
        toastr.success(result.msg);
        brands_table.ajax.reload();
    } else {
        toastr.error(result.msg);
    }
</script>
@endsection