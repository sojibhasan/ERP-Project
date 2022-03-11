@component('components.widget', ['class' => 'box-primary left_side'])

@php
$dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
$today = date("Y-m-d");
$diff = date_diff(date_create($dateOfBirth), date_create($today));
$age = $diff->format('%y');
$sidebar_setting = App\SiteSettings::where('id', 1)->select('ls_side_menu_bg_color', 'ls_side_menu_font_color',
'sub_module_color', 'sub_module_bg_color')->first();
@endphp
<style>
  .profile_image {
    background: {{$sidebar_setting->ls_side_menu_bg_color}};
  }
  </style>
<div class="col-md-12" style="margin:0px; padding:0px;">
 
  <div class="profile_image text-=center">
    <a href="{{action('PatientController@edit', $patient_details->user_id)}}" class="btn btn-xs btn-primary pull-right">Edit</a>
    <img class="img_profile" src="@if(empty($patient_details->profile_image)){{asset('/img/default.png')}} @else {{url($patient_details->profile_image)}} @endif" alt="">
    <h3>{{$patient_details->name}}</h3>
  </div>
  @php
      $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
      $marital_statuss = ['Married', 'UnMarried', ''];
      $patient_boold_group = $blood_groups[$patient_details->blood_group-1]; 
      $patient_martial_status = $marital_statuss[$patient_details->marital_status-1]; 
      $patient_code = App\User::where('id', $patient_details->user_id)->first();
  @endphp
  <div class="info_div">
    <ul>
      <li>@lang('patient.name') : <span style="font-wight: bold;">{{$patient_details->name}}</span></li>
      <li>@lang('patient.patient_code') : <span style="font-wight: bold;">{{!empty($patient_code)?$patient_code->username: ''}}</span></li>
      <li>@lang('patient.age') : <span style="font-wight: bold;">{{$age}}</span></li>
      <li>@lang('patient.gender') : <span style="font-wight: bold;">{{$gender}}</span></li>
      <li>@lang('patient.phone') : <span style="font-wight: bold;">{{$patient_details->mobile}}</span></li>
      <li>@lang('patient.blood_group') : <span style="font-wight: bold;">{{$patient_boold_group}}</span></li>
      <li>@lang('patient.marital_status') : <span style="font-wight: bold;">{{$patient_martial_status}}</span></li>
      <li>@lang('patient.height') : <span style="font-wight: bold;">{{$patient_details->height}}</span></li>
      <li>@lang('patient.weight') : <span style="font-wight: bold;">{{$patient_details->weight}}</span></li>

    </ul>
  </div>
</div>
@endcomponent