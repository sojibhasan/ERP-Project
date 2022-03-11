@extends('layouts.app')

@section('title', __( 'patient.add_family_member' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'patient.add_family_member' )</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action('FamilyController@store'), 'method' => 'post', 'id' => 'user_add_form' ]) !!}
  @component('components.widget')
  <fieldset>
      <div id="patient_register" style="border-radius: 5px;">
  
          @php
          $startingPatientPrefix = App\System::getProperty('patient_prefix');
          $startingPatientID = App\System::getProperty('patient_code_start_from');
          $currentID = App\PatientDetail::all()->count();
  
   
          $last_contact_id = DB::table('contacts')->select('id','contact_id')->orderBy('id', 'desc')->first();
          if(empty($startingPatientPrefix)){
                $startingPatientPrefix = '';
              }
              if(empty($currentID)){
                $p_id = $startingPatientPrefix.$startingPatientID;
              }else{
                $currentID++;
                $currentID = sprintf("%04d", $currentID);
                $p_id = '0001';
                if(empty($startingPatientPrefix)){
                  $p_id = $currentID;
                }else{
                  $p_id = $startingPatientPrefix .''.$currentID;
                }
          }
    
          @endphp
          <style>
              label {
                  color: black;
                  font-weight: 800;
              }
          </style>
          <div class="modal-body body2">
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          {!! Form::label('join_date', __('superadmin::lang.date'), '') !!}
                          <input type="text" name="join_date" id="join_date" value="{{ date('Y-m-d', time()) }}"
                              class="form-control" readonly="readonly" required placeholder="Date" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Patient Code <span class="requerdfield">*</span></label>
                          <input type="text" name="patient_id" class="form-control"
                              value="{{$p_id }}" readonly="readonly" required />
                          <input type="hidden" name="startingPatientID" value="{{ $p_id }}" placeholder="Patient ID" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Name <span class="requerdfield">*</span></label>
                          <input type="text" name="name" id="name" class="form-control" required placeholder="Name" />
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Address <span class="requerdfield">*</span></label>
                          <input type="text" name="address" id="address" class="form-control" required  placeholder="Address"/>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>City <span class="requerdfield">*</span></label>
                          <input type="text" name="city" id="city" class="form-control" required placeholder="City" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>State <span class="requerdfield">*</span></label>
                          <input type="text" name="state" id="state" class="form-control" required placeholder="State" />
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Country <span class="requerdfield">*</span></label>
                          {{-- <select name="country" id="country" class="form-control">
                                  <option value="">Select Country</option>
                                  @php
                                  // $countryResult	= $this->db->get_where('countries');
                                  // $countryData	= $countryResult->result();
                                  // if (!empty($countryData)) {
                                  //     foreach ($countryData as $row) {
                                  //         echo '<option value="' . $row->id . '" data-code="' . $row->country_code . '">' . $row->country_name . '</option>';
                                  //     }
                                  // }
                                  @endphp
                              </select> --}}
                          {!! Form::text('country', null, ['class' => 'form-control','placeholder' =>
                          __('business.country'), 'required']); !!}
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Mobile Phone <span class="requerdfield">*</span></label>
                          <input type="text" name="mobile" id="mobile" class="form-control" required placeholder="Mobile" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Email</label>
                          <input type="email" name="email" id="email" class="form-control" placeholder="Email" />
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Date of Birth <span class="requerdfield">*</span></label>
                          <input type="text" name="date_of_birth" id="p_date_of_birth" class="form-control" required placeholder="Date of birth" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Password <span class="requerdfield">*</span></label>
                          <input type="password" name="p_password" id="p_password" class="form-control" required placeholder="Password" />
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Reenter Password <span class="requerdfield">*</span></label>
                          <input type="password" name="p_confirm_password" id="confirm_password" class="form-control" placeholder="Confrim Password"
                              required />
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Gender <span class="requerdfield">*</span></label>
                          <select name="gender" id="gender" class="form-control" required>
                              <option value="">Select Gender</option>
                              <option value="1">Male</option>
                              <option value="2">Female</option>
                              <option value="3">Other</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Marital status <span class="requerdfield">*</span></label>
                          <select name="marital_status" id="marital_status" class="form-control" required>
                              <option value="">Select Marital status</option>
                              <option value="1">Married</option>
                              <option value="2">UnMarried</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Blood Group</label>
                          <select name="blood_group" id="blood_group" class="form-control">
                              <option value="">Select Blood Group</option>
                              <option value="1">AB-</option>
                              <option value="2">A-</option>
                              <option value="3">B-</option>
                              <option value="4">O-</option>
                              <option value="5">AB+</option>
                              <option value="6">A+</option>
                              <option value="7">B+</option>
                              <option value="8">O+</option>
                              <option value="9">Not Known</option>
                          </select>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Time / Zone</label>
                          <select name="time_zone" class="form-control">
                              <option>Time / Zone</option>
                              <option value="Etc/GMT+12">(GMT-12:00) International Date Line West</option>
                              <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                              <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                              <option value="US/Alaska">(GMT-09:00) Alaska</option>
                              <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                              <option value="America/Tijuana">(GMT-08:00) Tijuana, Baja California</option>
                              <option value="US/Arizona">(GMT-07:00) Arizona</option>
                              <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                              <option value="US/Mountain">(GMT-07:00) Mountain Time (US & Canada)</option>
                              <option value="America/Managua">(GMT-06:00) Central America</option>
                              <option value="US/Central">(GMT-06:00) Central Time (US & Canada)</option>
                              <option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                              <option value="Canada/Saskatchewan">(GMT-06:00) Saskatchewan</option>
                              <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                              <option value="US/Eastern">(GMT-05:00) Eastern Time (US & Canada)</option>
                              <option value="US/East-Indiana">(GMT-05:00) Indiana (East)</option>
                              <option value="Canada/Atlantic">(GMT-04:00) Atlantic Time (Canada)</option>
                              <option value="America/Caracas">(GMT-04:00) Caracas, La Paz</option>
                              <option value="America/Manaus">(GMT-04:00) Manaus</option>
                              <option value="America/Santiago">(GMT-04:00) Santiago</option>
                              <option value="Canada/Newfoundland">(GMT-03:30) Newfoundland</option>
                              <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                              <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires, Georgetown</option>
                              <option value="America/Godthab">(GMT-03:00) Greenland</option>
                              <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                              <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                              <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                              <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                              <option value="Africa/Casablanca">(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
                              <option value="Etc/Greenwich">(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon,
                                  London</option>
                              <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm,
                                  Vienna</option>
                              <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana,
                                  Prague</option>
                              <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                              <option value="Europe/Sarajevo">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
                              <option value="Africa/Lagos">(GMT+01:00) West Central Africa</option>
                              <option value="Asia/Amman">(GMT+02:00) Amman</option>
                              <option value="Europe/Athens">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                              <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                              <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                              <option value="Africa/Harare">(GMT+02:00) Harare, Pretoria</option>
                              <option value="Europe/Helsinki">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius
                              </option>
                              <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                              <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                              <option value="Africa/Windhoek">(GMT+02:00) Windhoek</option>
                              <option value="Asia/Kuwait">(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
                              <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                              <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                              <option value="Asia/Tbilisi">(GMT+03:00) Tbilisi</option>
                              <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                              <option value="Asia/Muscat">(GMT+04:00) Abu Dhabi, Muscat</option>
                              <option value="Asia/Baku">(GMT+04:00) Baku</option>
                              <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                              <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                              <option value="Asia/Yekaterinburg">(GMT+05:00) Yekaterinburg</option>
                              <option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi, Tashkent</option>
                              <option value="Asia/Calcutta">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                              <option value="Asia/Calcutta">(GMT+05:30) Sri Jayawardenapura</option>
                              <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                              <option value="Asia/Almaty">(GMT+06:00) Almaty, Novosibirsk</option>
                              <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                              <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                              <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                              <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                              <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                              <option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option>
                              <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                              <option value="Australia/Perth">(GMT+08:00) Perth</option>
                              <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                              <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                              <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                              <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                              <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                              <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                              <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                              <option value="Australia/Canberra">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                              <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                              <option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option>
                              <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                              <option value="Asia/Magadan">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
                              <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                              <option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                              <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                          </select>
                      </div>
                      <div class="col-md-6" style="padding-left: 0px;">
                          <div class="form-group">
                              <label>Height</label>
                              <input type="text" name="height" id="height" class="form-control" placeholder="Height" />
                          </div>
                      </div>
                      <div class="col-md-6" style="padding-right: 0px;">
                          <div class="form-group">
                              <label>Weight</label>
                              <input type="text" name="weight" id="weight" class="form-control" placeholder="Weight" />
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Guardian Name</label>
                          <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Guardian Name" />
                      </div>
                      <div class="form-group">
                          <label>Any Known Allergies</label>
                          <input type="text" name="known_allergies" id="known_allergies" class="form-control" placeholder="Knwon Allergies" />
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                          <label>Image Upload</label>
                          <img id="thumbnil" src="{{asset('/img/default.png')}}" alt="NoImage" title="NoImage"
                              class="img-responsive image-border" />
                      </div>
                      <div class="form-group">
                          <input type="file" name="fileToImage" id="fileToImage" accept="image/*"
                              onchange="showMyImage(this)" style="border: none;padding: 0px;margin: 0px;">
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                          <label>Payment slip</label>
                          <img id="thumbnil_slip" src="{{asset('/img/default.png')}}" alt="NoImage" title="NoImage"
                              class="img-responsive image-border" />
                      </div>
                      <div class="form-group">
                          <input type="file" name="fileToPayment" id="fileToPayment" accept="image/*"
                              onchange="showPaymentSlip(this)" style="border:none;padding:0px;margin:0px;">
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                          <label>Notes</label>
                          <textarea name="notes" class="form-control" placeholder="Notes"></textarea>
                      </div>
                  </div>
              </div>
          </div>
          
      </div>
 
  </fieldset>
  @endcomponent
  <div class="row">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" id="submit_user_button">@lang( 'messages.save' )</button>
    </div>
  </div>
  {!! Form::close() !!}
  @stop
  @section('javascript')
  <script type="text/javascript">
    $(document).ready(function(){
    $('#selected_contacts').on('ifChecked', function(event){
      $('div.selected_contacts_div').removeClass('hide');
    });
    $('#selected_contacts').on('ifUnchecked', function(event){
      $('div.selected_contacts_div').addClass('hide');
    });
  });

  $('form#user_add_form').validate({
                rules: {
                    first_name: {
                        required: true,
                    },
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
                    p_password: {
                        required: true,
                        minlength: 5
                    },
                    p_confirm_password: {
                        equalTo: "#p_password"
                    },
                    username: {
                        minlength: 5,
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
                    p_password: {
                        minlength: 'Password should be minimum 5 characters',
                    },
                    p_confirm_password: {
                        equalTo: 'Should be same as password'
                    },
                    username: {
                        remote: 'Invalid username or User already exist'
                    },
                    email: {
                        remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
                    }
                }
            });
  $('#username').change( function(){
    if($('#show_username').length > 0){
      if($(this).val().trim() != ''){
        $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "</b>");
      } else {
        $('#show_username').html('');
      }
    }
  });
  </script>
  @endsection