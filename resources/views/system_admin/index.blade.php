@extends('layouts.app')
@section('title', __( 'lang_v1.system_administrations'))
@section('content')
<!-- Editor CSS file -->
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'site_settings.site_settings')
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'site_settings.settings')])
    {{-- @if(auth()->user()->can('direct_sell.access'))) --}}
    <form action="{{route('site_settings.update_settings')}}" method="post" id="settingForm"
        enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>
                                    {{$error}}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-4" style="padding:0px;">
                                    <div class="form-group">
                                        <label>Favicon</label><br />
                                        <input id="uploadFileFicon" style="height:40px;border:1px solid #ccc"
                                            readonly="readonly" />
                                        <div class="fileUpload btn btn-primary" style="padding: 9px 12px;">
                                            <span>Browse</span>
                                            <input id="uploadBtnFicon" name="uploadFileFicon" type="file"
                                                class="upload" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label>Preview:</label>
                                    <img src="@if($settings->uploadFileFicon != ''){{url($settings->uploadFileFicon)}} @else {{asset('public/img/NoImage.png')}} @endif"
                                        height="100px" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-4" style="padding:0px;">
                                    <div class="form-group">
                                        <label>Login Page Background</label><br />
                                        <input id="uploadFileLBackground" readonly
                                            style="height: 40px;border: 1px solid #ccc" />
                                        <div class="fileUpload btn btn-primary" style="padding: 9px 12px;">
                                            <span>Browse</span>
                                            <input id="uploadBtnLBackground" name="uploadFileLBackground" type="file"
                                                class="upload" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label>Preview:</label>
                                    <img src="@if($settings->uploadFileLBackground != ''){{url($settings->uploadFileLBackground)}} @else {{asset('public/img/NoImage.png')}} @endif"
                                        height="100px" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-4" style="padding:0px;">
                                    <div class="form-group">
                                        <label>Login Page Logo</label><br />
                                        <input id="uploadFileLLogo" readonly
                                            style="height: 40px;border: 1px solid #ccc" />
                                        <div class="fileUpload btn btn-primary" style="padding: 9px 12px;">
                                            <span>Browse</span>
                                            <input id="uploadBtnLLogo" name="uploadFileLLogo" type="file"
                                                class="upload" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding:0px;">
                                    <div class="col-md-6">
                                        <label>Width:</label>
                                        <input id="logingLogo_width" name="logingLogo_width" placeholder="e.g.100"
                                            type="text" class="form-control" value="{{$settings->logingLogo_width}}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Height:</label>
                                        <input id="logingLogo_height" name="logingLogo_height" placeholder="e.g.100"
                                            type="text" class="form-control" value="{{$settings->logingLogo_height}}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Preview:</label>
                                    <img src="@if($settings->uploadFileLLogo != ''){{url($settings->uploadFileLLogo)}} @else {{asset('public/img/NoImage.png')}} @endif"
                                        height="100px" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Login Page Background Color</label>
                                    <input type="text" name="login_background_color"
                                        value="{{$settings->login_background_color}}" class="form-control" autofocus
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Login Page Login Box Color</label>
                                    <input type="text" name="login_box_color" value="{{$settings->login_box_color}}"
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Top belt Background Color</label>
                                    <input type="text" name="topBelt_background_color"
                                        value="{{$settings->topBelt_background_color}}" class="form-control" autofocus
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Login Page Showing Type</label>
                                    <select name="background_showing_type" id="background_showing_type"
                                        class="form-control">
                                        <option value="1" @if($settings->background_showing_type == '1' ){{'selected'}}
                                            @endif>Only Background Image</option>
                                        <option value="2" @if($settings->background_showing_type == '2' ){{'selected'}}
                                            @endif>Only Background Color</option>
                                        <option value="3" @if($settings->background_showing_type == '3' ){{'selected'}}
                                            @endif>Background Color & Image</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sales Agents Registration</label>
                                    <select name="sales_agents_registration" id="sales_agents_registration"
                                        class="form-control">
                                        <option value="1" @if($settings->sales_agents_registration == '1'
                                            ){{'selected'}} @endif>Enabled</option>
                                        <option value="0" @if($settings->sales_agents_registration == '0'
                                            ){{'selected'}} @endif>Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Main module Color</label>
                                    <input type="text" name="main_module_color" value="{{$settings->main_module_color}}"
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sub module Color</label>
                                    <input type="text" name="sub_module_color" value="{{$settings->sub_module_color}}"
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sub Module Background Color</label>
                                    <input type="text" name="sub_module_bg_color"
                                        value="{{$settings->sub_module_bg_color}}" class="form-control" autofocus
                                        autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Left side Menu Background Colour</label>
                                    <input type="text" name="ls_side_menu_bg_color"
                                        value="{{$settings->ls_side_menu_bg_color}}" class="form-control" autofocus
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Left side Menu Font Colour</label>
                                    <input type="text" name="ls_side_menu_font_color"
                                        value="{{$settings->ls_side_menu_font_color}}" class="form-control" autofocus
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 30px;">
                                    <label>
                                        {!! Form::checkbox('tc_sale_and_pos', 1,
                                        $settings->tc_sale_and_pos,
                                        [ 'class' => 'input-icheck']); !!} T&C for Sales / POS invoice
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <label style="padding-left: 14px;">Tab Background Color:</label>
                            <br>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Register Now</label>
                                        <input type="text" name="register_now_btn_bg"
                                            value="{{$settings->register_now_btn_bg}}" class="form-control" autofocus
                                            autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Customer Register</label>
                                        <input type="text" name="customer_register_btn_bg"
                                            value="{{$settings->customer_register_btn_bg}}" class="form-control"
                                            autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Member Register</label>
                                        <input type="text" name="member_register_btn_bg"
                                            value="{{$settings->member_register_btn_bg}}" class="form-control" autofocus
                                            autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Pricing</label>
                                        <input type="text" name="pricing_btn_bg" value="{{$settings->pricing_btn_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Member Register</label>
                                        <input type="text" name="member_register_bg" value="{{$settings->member_register_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Self Register</label>
                                        <input type="text" name="self_register_bg" value="{{$settings->self_register_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Admin Login</label>
                                        <input type="text" name="admin_login_bg" value="{{$settings->admin_login_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Customer Login</label>
                                        <input type="text" name="customer_login_bg" value="{{$settings->customer_login_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Member Login</label>
                                        <input type="text" name="member_login_bg" value="{{$settings->member_login_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Employee Login</label>
                                        <input type="text" name="employee_login_bg" value="{{$settings->employee_login_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Visitor Login</label>
                                        <input type="text" name="visitor_login_bg" value="{{$settings->visitor_login_bg}}"
                                            class="form-control" autofocus autocomplete="off" />
                                    </div>
                                </div>
                        </div>
                        @php
                        $messages_show = json_decode($settings->show_messages);
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Login page show messages?</label><br />
                                    <input type="checkbox" name="show_messages[lp_title]"
                                        class="show_messages input-icheck" value="1"
                                        @if(!empty($messages_show->lp_title) == 1) {{'checked'}} @endif />Login Page
                                    Title<br /><br />
                                    <input type="checkbox" name="show_messages[lp_text]"
                                        class="show_messages input-icheck" value="1" @if(!empty($messages_show->lp_text)
                                    == 1) {{'checked'}} @endif /> Login PageFooter Text<br /><br />
                                    <input type="checkbox" name="show_messages[lp_description]"
                                        class="show_messages input-icheck" value="1"
                                        @if(!empty($messages_show->lp_description) == 1) {{'checked'}} @endif /> Login
                                    Page Description<br /><br />
                                    <input type="checkbox" name="show_messages[lp_system_message]"
                                        class="show_messages input-icheck" value="1"
                                        @if(!empty($messages_show->lp_system_message) == 1) {{'checked'}} @endif />
                                    System general message<br /><br />
                                    <input type="checkbox" name="show_messages[lp_system_expired]"
                                        class="show_messages input-icheck" value="1"
                                        @if(!empty($messages_show->lp_system_expired) == 1) {{'checked'}} @endif />
                                    System Expired Message
                                </div>
                            </div>
                            <div class="col-md-6" id="lp_title">
                                <div class="form-group">
                                    <label>Login Page Title</label>
                                    <textarea id="login_page_title" name="login_page_title"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6" id="lp_text">
                                <div class="form-group">
                                    <label>Login Page Footer Text</label>
                                    <textarea id="login_page_footer" name="login_page_footer"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6" id="lp_description">
                                <div class="form-group">
                                    <label>Login Page Description</label>
                                    <textarea id="login_page_description" name="login_page_description"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6" id="lp_system_message">
                                <div class="form-group">
                                    <label>System general message</label>
                                    <textarea id="login_page_general_message"
                                        name="login_page_general_message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6" id="lp_system_expired">
                                <div class="form-group">
                                    <label>System Expired Message</label>
                                    <textarea id="system_expired_message" name="system_expired_message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="hero-unit">
                                    <label>Invoice Footer Text</label>
                                    <textarea id="invoice_footer" name="invoice_footer"></textarea>
                                </div>
                            </div>
							<div class="col-md-6">
                                <div class="captch_site_key">
                                    <label for="captch_site_key">reCAPTCHA Site Key</label>
                                    <textarea id="captch_site_key" class="form-control" name="captch_site_key">{{$settings->captch_site_key}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button class="btn btn-primary" id="settingForm_button">&nbsp;&nbsp;Update System
                                        Setting&nbsp;&nbsp;</button>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- @endif --}}
    @endcomponent
</section>
<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->
<style>
    .fileUpload {
        position: relative;
        overflow: hidden;
        border-radius: 0;
        margin-left: -4px;
        margin-top: -2px
    }
    .fileUpload input.upload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0)
    }
</style>
@stop
@section('javascript')
<script src="{{asset('js/editor.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        document.getElementById("uploadBtnFicon").onchange = function () {
            document.getElementById("uploadFileFicon").value = this.value;
        };
        document.getElementById("uploadBtnLLogo").onchange = function () {
            document.getElementById("uploadFileLLogo").value = this.value;
        };
        document.getElementById("uploadBtnLBackground").onchange = function () {
            document.getElementById("uploadFileLBackground").value = this.value;
        };
        document.getElementById("uploadBtnpos").onchange = function () {
            document.getElementById("uploadpos").value = this.value;
        };
        document.getElementById("uploadBtnSales").onchange = function () {
            document.getElementById("uploadSales").value = this.value;
        };
        document.getElementById("uploadBtnOutlets").onchange = function () {
            document.getElementById("uploadOutlets").value = this.value;
        };
        document.getElementById("uploadBtnUsers").onchange = function () {
            document.getElementById("uploadFileUsers").value = this.value;
        };
        document.getElementById("uploadBtnSetting").onchange = function () {
            document.getElementById("uploadFileSetting").value = this.value;
        };
        document.getElementById("uploadBtnReport").onchange = function () {
            document.getElementById("uploadFileReport").value = this.value;
        };
    });
    $("#invoice_footer").Editor();
    $("#invoice_footer").Editor("setText",'{{$settings->invoice_footer}}');
	$("#login_page_title").Editor();
    $("#login_page_title").Editor("setText",'{{$settings->login_page_title}}');
	$("#login_page_footer").Editor();
    $("#login_page_footer").Editor("setText",'{{$settings->login_page_footer}}');
	$("#login_page_description").Editor();
    $("#login_page_description").Editor("setText",'{{$settings->login_page_description}}');
	$("#login_page_general_message").Editor();
    $("#login_page_general_message").Editor("setText",'{{$settings->login_page_general_message}}');
	$("#system_expired_message").Editor();
    $("#system_expired_message").Editor("setText",'{{$settings->system_expired_message}}');
$('#settingForm_button').click(function(e){
    e.preventDefault();
	var editor_html = $("#invoice_footer").Editor("getText").replace('<p>', '').replace('</p>', '');
	$("#invoice_footer").val(editor_html);
	var editor_html1 = $("#login_page_title").Editor("getText").replace('<p>', '').replace('</p>', '');
	$("#login_page_title").val(editor_html1);
	var editor_html2 = $("#login_page_footer").Editor("getText").replace('<p>', '').replace('</p>', '');
	$("#login_page_footer").val(editor_html2);
	var editor_html3 = $("#login_page_description").Editor("getText").replace('<p>', '').replace('</p>', '');
	$("#login_page_description").val(editor_html3);
	var editor_html5 = $("#login_page_general_message").Editor("getText").replace('<p>', '').replace('</p>', '');
	$("#login_page_general_message").val(editor_html5);
	var editor_html4 = $("#system_expired_message").Editor("getText").replace('<p>', '').replace('</p>', '');
    $("#system_expired_message").val(editor_html4);
    $('#settingForm').submit();
});
</script>
@endsection