@extends('layouts.app')
@section('title', __('business.business_settings'))
@php
$business_or_entity = App\System::getProperty('business_or_entity');
@endphp
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@if($business_or_entity == 'business'){{ __('business.business_settings') }} @endif @if($business_or_entity == 'entity'){{ __('lang_v1.entity_settings') }} @endif</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<style>
    .select2-results__option[aria-selected="true"] {
        display: none;
    }

    .equal-column {
        min-height: 95px;
    }
</style>
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('BusinessController@postBusinessSettings'), 'method' => 'post', 'id' =>
    'bussiness_edit_form',
    'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
            <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('business.business')</a>
                        <a href="#" class="list-group-item text-center">@lang('business.tax')
                            @show_tooltip(__('tooltip.business_tax'))</a>
                       
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('business.product')</a>
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('business.sale')</a>
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('sale.pos_sale')</a>
                       
                        <a href="#" class="list-group-item text-center">@lang('purchase.purchases')</a>
                        @if(!config('constants.disable_expiry', true))
                        <a href="#" class="list-group-item text-center">@lang('business.dashboard')</a>
                        @endif
                        <a href="#" class="list-group-item text-center">@lang('business.system')</a>
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.prefixes')</a>
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.email_settings')</a>
                        {{-- @if(!empty($package_permissions))
                        @if($package_permissions->sms_settings_access)
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.sms_settings')</a>
                        @endif
                        @endif --}}
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('lang_v1.reward_point_settings')</a>
                        @if($get_permissions['access_module'] == 1)
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.modules')</a>
                        @endif
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('lang_v1.custom_labels')</a>
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('store.stores')</a>
                        @if($get_permissions['restaurant'] == 1)
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('lang_v1.restaurant')</a>
                        @endif
                        @if($get_permissions['booking'] == 1)
                        <a href="#" class="@if($get_permissions['property_module'] == 1) hide  @endif list-group-item text-center">@lang('lang_v1.booking')</a>
                        @endif
                        @if($get_permissions['upload_images'] == 1)
                        @can('upload_images')
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.upload_images')</a>
                        @endcan
                        @endif
                      
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.customer_and_supplier')</a>
                 
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <!-- tab 1 start -->
                    @include('business.partials.settings_business')
                    <!-- tab 1 end -->
                    <!-- tab 2 start -->
                    @include('business.partials.settings_tax')
                    <!-- tab 2 end -->
                    <!-- tab 3 start -->
                    @include('business.partials.settings_product')
                    <!-- tab 3 end -->
                    <!-- tab 4 start -->
                    @include('business.partials.settings_sales')
                    @include('business.partials.settings_pos')
                    <!-- tab 4 end -->
                    <!-- tab 5 start -->
                    @include('business.partials.settings_purchase')
                    <!-- tab 5 end -->
                    <!-- tab 6 start -->
                    @if(!config('constants.disable_expiry', true))
                    @include('business.partials.settings_dashboard')
                    @endif
                    <!-- tab 6 end -->
                    <!-- tab 7 start -->
                    @include('business.partials.settings_system')
                    <!-- tab 7 end -->
                    <!-- tab 8 start -->
                    @include('business.partials.settings_prefixes')
                    <!-- tab 8 end -->
                    <!-- tab 9 start -->
                    @include('business.partials.settings_email')
                    <!-- tab 9 end -->
                    <!-- tab 10 start -->
                    {{-- @if(!empty($package_permissions)) --}}
                    {{-- @if($package_permissions->sms_settings_access) --}}
                    {{-- @include('business.partials.settings_sms') --}}
                    {{-- @endif --}}
                    {{-- @endif --}}
                    <!-- tab 10 end -->
                    <!-- tab 11 start -->
                    @include('business.partials.settings_reward_point')
                    <!-- tab 11 end -->
                    <!-- tab 12 start -->
                    @if($get_permissions['access_module'] == 1)
                    @include('business.partials.settings_modules')
                    @endif
                    <!-- tab 12 end -->
                    @include('business.partials.settings_custom_labels')
                    <!-- tab 9 start -->
                    @include('business.partials.settings_store')
                    <!-- tab 9 end -->
                    <!-- tab 13 end -->
                    @if($get_permissions['restaurant'] == 1)
                    @include('business.partials.settings_restaurant')
                    @endif
                    <!-- tab 14 end -->
                    @if($get_permissions['booking'] == 1)
                    @include('business.partials.settings_booking')
                    @endif
                     <!-- tab 15 end -->
                     @if($get_permissions['upload_images'] == 1)
                     @can('upload_images')
                     @include('business.partials.settings_upload_images')
                     @endcan
                     @endif
                    <!-- tab 15 end -->
           
                     <!-- tab 16 end -->
                    @include('business.partials.customer_and_supplier')
                     <!-- tab 16 end -->
                   
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button"
                type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{asset('js/editor.js')}}"></script>
<script>
    $("#tc_sale_and_pos_text").Editor();
    $("#tc_sale_and_pos_text").Editor("setText",'@if(!empty($pos_settings["tc_sale_and_pos_text"])){{$pos_settings["tc_sale_and_pos_text"]}} @endif');
    $('.settingForm_button').click(function(e){
        var editor_html1 = $("#tc_sale_and_pos_text").Editor("getText").replace('<p>', '').replace('</p>', '');
        $("#tc_sale_and_pos_text").val(editor_html1);
    });
</script>


<script type="text/javascript">
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });

    $('#test_email_btn').click( function() {
        var data = {
            mail_driver: $('#mail_driver').val(),
            mail_host: $('#mail_host').val(),
            mail_port: $('#mail_port').val(),
            mail_username: $('#mail_username').val(),
            mail_password: $('#mail_password').val(),
            mail_encryption: $('#mail_encryption').val(),
            mail_from_address: $('#mail_from_address').val(),
            mail_from_name: $('#mail_from_name').val(),
        };
        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action('BusinessController@testEmailConfiguration') }}",
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
        });
    });

    $('#test_sms_btn').click( function() {
        var test_number = $('#test_number').val();
        if (test_number.trim() == '') {
            toastr.error('{{__("lang_v1.test_number_is_required")}}');
            $('#test_number').focus();

            return false;
        }

        var data = {
            url: $('#sms_settings_url').val(),
            send_to_param_name: $('#send_to_param_name').val(),
            msg_param_name: $('#msg_param_name').val(),
            request_method: $('#request_method').val(),
            param_1: $('#sms_settings_param_key1').val(),
            param_2: $('#sms_settings_param_key2').val(),
            param_3: $('#sms_settings_param_key3').val(),
            param_4: $('#sms_settings_param_key4').val(),
            param_5: $('#sms_settings_param_key5').val(),
            param_6: $('#sms_settings_param_key6').val(),
            param_7: $('#sms_settings_param_key7').val(),
            param_8: $('#sms_settings_param_key8').val(),
            param_9: $('#sms_settings_param_key9').val(),
            param_10: $('#sms_settings_param_key10').val(),

            param_val_1: $('#sms_settings_param_val1').val(),
            param_val_2: $('#sms_settings_param_val2').val(),
            param_val_3: $('#sms_settings_param_val3').val(),
            param_val_4: $('#sms_settings_param_val4').val(),
            param_val_5: $('#sms_settings_param_val5').val(),
            param_val_6: $('#sms_settings_param_val6').val(),
            param_val_7: $('#sms_settings_param_val7').val(),
            param_val_8: $('#sms_settings_param_val8').val(),
            param_val_9: $('#sms_settings_param_val9').val(),
            param_val_10: $('#sms_settings_param_val10').val(),
            test_number: test_number
        };

        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action('BusinessController@testSmsConfiguration') }}",
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
        });
    });

    $('.select2').select2();

    $('#show_for_customers').on('ifChecked', function (event) {
        $('.business_categories_div').removeClass('hide');
    });
    $('#show_for_customers').on('ifUnChecked', function (event) {
        $('.business_categories_div').addClass('hide');
    });
</script>
@endsection