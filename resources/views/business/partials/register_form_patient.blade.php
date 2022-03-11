@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$login_background_color = $settings->login_background_color;
@endphp
<style>
    #patient_register {
        background: {
                {
                $login_background_color
            }
        }
         !important;
    }
    label {
        color: white !important;
    }
</style>
<fieldset>
    <div id="patient_register" style="border-radius: 5px; background: #445867 !important;">
        @php
        $startingPatientPrefix = App\System::getProperty('patient_prefix');
        $startingPatientID = App\System::getProperty('patient_code_start_from');
        $currentID = App\PatientDetail::all()->count();
        $nummber_of_c = strlen($startingPatientID );
        if(empty($startingPatientPrefix)){
        $startingPatientPrefix = '';
        }
        if(!empty($startingPatientID)){
        $currentID = $currentID + (int)$startingPatientID;
        }
        if(empty($currentID)){
        $p_id = $startingPatientPrefix.$startingPatientID;
        }else{
        $currentID++;
        $currentID = sprintf("%0".$nummber_of_c."d", $currentID);
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
            {!! Form::hidden('package_id', null, ['class' => 'package_id']); !!}
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
                        <label>First Name <span class="requerdfield">*</span></label>
                        <input type="text" name="first_name" id="p_first_name" class="form-control" required
                            placeholder="First Name" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Last Name <span class="requerdfield">*</span></label>
                        <input type="text" name="last_name" id="p_last_name" class="form-control" required
                            placeholder="Last Name" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Address <span class="requerdfield">*</span></label>
                        <input type="text" name="address" class="form-control" required
                            placeholder="Address" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>City <span class="requerdfield">*</span></label>
                        <input type="text" name="city" class="form-control" required placeholder="City" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>State <span class="requerdfield">*</span></label>
                        <input type="text" name="state" id="p_state" class="form-control" required placeholder="State" />
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
                        __('business.country'), 'required', 'id' => 'p_country']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Mobile Phone <span class="requerdfield">*</span></label>
                        <input type="text" name="mobile" id="p_mobile" class="form-control" required
                            placeholder="Mobile" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="p_email" id="p_email" class="form-control" placeholder="Email" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date of Birth <span class="requerdfield">*</span></label>
                        <input type="text" name="date_of_birth" id="p_date_of_birth" class="form-control" required
                            placeholder="Date of birth" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Password <span class="requerdfield">*</span></label>
                        <input type="password" name="p_password" id="p_password" class="form-control" required
                            placeholder="Password" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Reenter Password <span class="requerdfield">*</span></label>
                        <input type="password" name="p_confirm_password" id="p_confirm_password" class="form-control"
                            placeholder="Confrim Password" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Gender <span class="requerdfield">*</span></label>
                        <select name="gender" id="p_gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
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
                        <input type="text" name="guardian_name" id="guardian_name" class="form-control"
                            placeholder="Guardian Name" />
                    </div>
                    <div class="form-group">
                        <label>Any Known Allergies</label>
                        <input type="text" name="known_allergies" id="known_allergies" class="form-control"
                            placeholder="Knwon Allergies" />
                    </div>
                    @if(in_array('my_health', $show_referrals_in_register_page ))
                    <div class="form-group">
                        {!! Form::label('referral_code', __('superadmin::lang.referral_code')) !!} <small>@lang('lang_v1.please_enter_referral_code_if_any')</small>
                        {!! Form::text('referral_code', 0, ['class' => 'form-control','placeholder' =>
                        __('superadmin::lang.referral_code'), 'style' => 'width: 100%;',
                        ]); !!}
                    </div>
                    @endif
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Image Upload</label>
                    </div>
                    <div class="form-group">
                        <input type="file" name="fileToImage" id="fileToImage" accept="image/*"
                            onchange="showMyImage(this)" style="border: none;padding: 0px;margin: 0px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Payment slip</label>
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
                    <div class="clearfix"></div>
                    @if(in_array('my_health', $show_give_away_gift_in_register_page ))
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('give_away_gifts', __('superadmin::lang.give_away_gifts') . ':') !!}
                            @foreach ($give_away_gifts as $key => $give_away_gift)
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('give_away_gifts[]', $key, false, ['class' => '']);
                                    !!} {{$give_away_gift}}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
				@if(!empty($business_settings->captch_site_key))
                    <div class="col-md-12">
                    <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
                    <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
                    </div>
				@endif
            </div>
        </div>
    </div>
    <div class="modal-footer" style="padding-top: 15px; padding-bottom: 0px;">
        <div class="row">
            <div class="col-md-6" style="text-align: left;">
                {{-- <div class="form-group">
                    <input type="checkbox" name="send_sms" value="1"> Send SMS
                </div> --}}
            </div>
            <div class="col-md-6" style="text-align: right;">
                <button class="btn btn-primary pull-right" type="submit">Submit</button>
            </div>
        </div>
    </div>
</fieldset>