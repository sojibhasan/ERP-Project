@extends('layouts.app')
@section('title', __('cheque.templates_create'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.templates_create')</h1>
    <style>
        .digit_box{background:#c1c1c1;padding:10px;border:1px solid #5f6468;color:#000}
        .digit_box2{background:#f1f1f1;padding:10px;border:1px solid #5f6468;color:#000}
        #wrapperDiv{margin-bottom:50px}
        #innerDiv{border:1px solid #000;height:600px;width:600px;background:#fff}
        .draggable:focus{outline:2px dashed #000}
        .resizable:focus{=outline:2px dashed #000}
        .resize{background:blue;border-radius:10px;border:2.5px solid #fff;width:5px;height:5px;position:absolute;box-shadow:0 0 2px blue;z-index:1;opacity:.9;-moz-user-select:none}
        .resize.nw{top:-5px;left:-5px;right:auto;bottom:auto;cursor:nwse-resize}
        .resize.n{top:-5px;left:calc(50% - 5px);right:auto;bottom:auto;cursor:ns-resize}
        .resize.ne{top:-5px;left:auto;right:-5px;bottom:auto;cursor:nesw-resize}
        .resize.w{top:calc(50% - 5px);left:-5px;right:auto;bottom:-5px;cursor:ew-resize}
        .resize.e{top:calc(50% - 5px);left:auto;right:-5px;bottom:-5px;cursor:ew-resize}
        .resize.sw{top:auto;left:-5px;right:auto;bottom:-5px;cursor:nesw-resize}
        .resize.s{top:auto;left:calc(50% - 5px);right:-5px;bottom:-5px;cursor:ns-resize}
        .resize.se{top:auto;left:auto;right:-5px;bottom:-5px;cursor:nwse-resize}
        @media (pointer: coarse) {
        .resize{width:15px;height:15px;border:7.5px solid #fff;border-radius:30px}
        .resize.nw{top:-15px;left:-15px;right:auto;bottom:auto;cursor:nwse-resize}
        .resize.n{top:-15px;left:calc(50% - 15px);right:auto;bottom:auto;cursor:ns-resize}
        .resize.ne{top:-15px;left:auto;right:-15px;bottom:auto;cursor:nesw-resize}
        .resize.w{top:calc(50% - 15px);left:-15px;right:auto;bottom:-15px;cursor:ew-resize}
        .resize.e{top:calc(50% - 15px);left:auto;right:-15px;bottom:-15px;cursor:ew-resize}
        .resize.sw{top:auto;left:-15px;right:auto;bottom:-15px;cursor:nesw-resize}
        .resize.s{top:auto;left:calc(50% - 15px);right:-15px;bottom:-15px;cursor:ns-resize}
        .resize.se{top:auto;left:auto;right:-15px;bottom:-15px;cursor:nwse-resize}
        }
        #addtemplateimg{background-image:url();background-size:cover;background-position:1px 15px;width:650px;margin:auto;height:320px;border:1px solid #bbb}
        .ruler,.ruler li{margin:0;padding:0;list-style:none;display:inline-block}
        .ruler{background:#ffffe0;box-shadow:0 -1px 1em hsl(60,60%,84%) inset;border-radius:2px;border:1px solid #ccc;color:#ccc;margin:0;height:3em;padding-right:1cm;white-space:nowrap}
        .ruler li{padding-left:1cm;width:5em;margin:.64em -1em -.64em;text-align:center;position:relative;text-shadow:1px 1px hsl(60,60%,84%)}
        .ruler li:before{content:'';position:absolute;border-left:1px solid #ccc;height:.64em;top:-.64em;right:1em}
        .margbotm{margin-bottom:5px;max-height:55px;min-height:50px;}
        .margbotm2{margin-bottom:5px;max-height:77px;min-height:75px;}
        .margbotm3{margin-bottom:5px;max-height:35px;min-height:32px;}
        .form-control{height:35px;}
        .number-control{height:25px;width: 90%;box-shadow: none !important;padding: 6px 12px;font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;}
        </style>    
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.templates_create')])
    <div class="col-md-12 main">
        <div class="row panel-body" style="padding: 5px;">
            <div class="col-md-3 margbotm">
                Template Name: <span style="color: #F00">*</span>
                <input type="text" name="template_name" id="template_name" class="form-control" maxlength="20" autofocus required autocomplete="off" />
            </div>
            <div class="col-md-3 margbotm">
                Copy Template:
                <select id="copyTemplate" class="form-control" onchange="copyTemplateChange()">
                    <option value="" >Select Template</option>
                    <?php foreach($templates as $val){ ?>
                        <option value="<?php echo $val->id; ?>"><?php echo $val->template_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 margbotm">
                Template Image: <span style="color: #F00">*</span>
                <form action="{{action('Chequer\ChequeTemplateController@uploadImageFile')}}" method="post" id="uplodafiles"
                enctype="multipart/form-data">
                <input type="hidden" id="tempid" name="tempid">
                <input type="file" name="userfile" id="template_image" class="form-control template_image" size="20"/>
                <input type="hidden" id="image_url" name="image_url">
            </div>
            <div class="col-md-3 margbotm">
                Set Date:<br/>
                <ul style="list-style:none;display:inline-flex;padding:0px;">
                    <li id="D1" class="digit_box" onclick="colorchange1()">d</li>
                    <li id="D2" class="digit_box" onclick="colorchange2()">d</li>
                    <li id="M1" class="digit_box" onclick="colorchange3()">m</li>
                    <li id="M2" class="digit_box" onclick="colorchange4()">m</li>
                    <li id="Y1" class="digit_box" onclick="colorchange5()">y</li>
                    <li id="Y2" class="digit_box" onclick="colorchange6()">y</li>
                    <li id="Y3" class="digit_box" onclick="colorchange7()">y</li>
                    <li id="Y4" class="digit_box" onclick="colorchange8()">y</li>
                </ul>
            </div>
        </div>
        <div class="row panel-body" style="padding: 5px;">
            <div class="col-md-3 margbotm2">
                Select Date Format:
                <p><select id="dateformat" class="form-control" onchange="datechange()">
                    <option value="">Select Date Format</option>
                    <option value="1">dd-mm-yyyy</option>
                    <option value="2">mm-dd-yyyy</option>
                    <option value="3">yyyy-mm-dd</option>
                </select></p>
                <p style="margin-bottom: 5px;"><label><input type="checkbox" id="words2" onclick="words()"> Amount in words 2</label></p>
            </div>
            <div class="col-md-3 margbotm2">
                Select Seprator:
                <p><select id="seprator" class="form-control" onchange="datechange()">
                    <option value="">Select Seprator</option>
                    <option value="">-</option>
                    <option value="">/</option>
                </select></p>
                <p style="margin-bottom: 5px;"><label><input type="checkbox" id="words3" onclick="words()"> Amount in words 3</label></p>
            </div>
            <div class="col-md-3 margbotm2">
                Template Size: <span style="color: #F00">*</span>
                <p style="margin-bottom: 5px;"><input type="number" step="0.01" name="template_width" id="template_width" placeholder="Width" min="1" max="1500" value="6.77" oninput="paymargin()" class="number-control"/> in</p>
                <p style="margin-bottom: 5px;"><input type="number" step="0.01" name="template_height" id="template_height" min="1" max="1500" placeholder="Height" value="3.33" oninput="paymargin()" class="number-control"/> in</p>
            </div>
            <div class="col-md-3 margbotm2">
                Set Position: <span style="color: #F00">*</span>
                <p style="margin-bottom: 5px;"><input type="number" step="0.01" name="move_top" id="move_top" min="0"  placeholder="margib top"  oninput="datemargin();" class="number-control"/> in</p>
                <p style="margin-bottom: 5px;"><input type="number" step="0.01" name="move_left" id="move_left" placeholder="margin left" min="0" oninput="datemargin();" class="number-control"/> in</p>
                <input type="hidden" id="div_id">
            </div>
        </div>
        <div class="row panel-body" style="padding: 5px;">
            <div class="col-md-3 margbotm3">
                <label><input type="checkbox" id="strikeBearerCheckbox" onclick="strikeBearer()"> Strike Bearer</label>
            </div>
            <div class="col-md-3 margbotm3">
                <label><input type="checkbox" id="StampCheckbox" onclick="Stamp()"> Stamp</label>
            </div>
            <div class="col-md-3 margbotm3">
                <label><input type="checkbox" id="doubleCross" onclick="doubleCrosses()"> Double cross</label>
            </div>
            <div class="col-md-3 margbotm3">
                <button type="submit" id="sendBtn" class="btn btn-primary" onclick="saveTemplate();" >Save </button>
            </div>
        </div>
    
        <div class="row">
            <div class="col-md-12">
                <div class="cheque-container" id="wrapperDiv">
                    <div id="addtemplateimg" >
                        <div id="pay" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left:50px;margin-top:78px;border: 1px solid black;width: 416px;" onclick="get_aligmentypay();">Name of Payee</div>
                        <div id="date" class="date_format draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 531px;margin-top: 17px;"></div>
                        <div id="d1" class=" draggable" onclick="get_aligmentd1();" contenteditable="true" style="position:absolute;color:black;margin-left: 447px;margin-top: 29px;border: 1px solid black;padding: 5px">d</div>
                        <div id="d2" class=" draggable" onclick="get_aligmentd2();" contenteditable="true" style="position:absolute;color:black; margin-left: 470px;margin-top: 29px;border: 1px solid black;padding: 5px">d</div>
                        <div id="ds1" class="draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 475px;margin-top: 35px;padding: 5px"></div>
                        <div id="m1" class="draggable" onclick="get_aligmentm1();" contenteditable="true" style="position:absolute;color:black;margin-left: 492px;margin-top: 29px;border: 1px solid black;padding: 5px">m</div>
                        <div id="m2" class=" draggable" onclick="get_aligmentm2();" contenteditable="true" style="position:absolute;color:black;margin-left: 518px;margin-top: 29px;border: 1px solid black;padding: 5px">m</div>
                        <div id="ds2" class="draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 544px;margin-top: 29px;padding: 5px"></div>
                        <div id="y1" class=" draggable" onclick="get_aligmenty1();" contenteditable="true" style="position:absolute;color:black;margin-left: 544px;margin-top: 29px;border: 1px solid black;padding: 5px">y</div>
                        <div id="y2" class=" draggable" onclick="get_aligmenty2();" contenteditable="true" style="position:absolute;color:black;margin-left: 566px;margin-top: 29px;border: 1px solid black;padding: 5px">y</div>
                        <div id="y3" class=" draggable" onclick="get_aligmenty3();" contenteditable="true"  style="position:absolute;color:black;margin-left: 587px;margin-top: 29px;border: 1px solid black;padding: 5px">y</div>
                        <div id="y4" class=" draggable" contenteditable="true" onclick="get_aligmenty4();" style="position:absolute;color:black;margin-left: 608px;margin-top: 29px;border: 1px solid black;padding: 5px">y</div>
                        <div id="amount" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 438px;margin-top: 141px;border: 1px solid black;width: 190px;" onclick="get_aligmentyamount();">Amount</div>
                        <div id="inWords1" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 55px;margin-top: 112px;border: 1px solid black;width: 335px;" onclick="get_aligmentywoprd1();">Amout in Words - Line one</div>
                        <div id="inWords2" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 47px;margin-top: 149px;display:none;border: 1px solid black;width: 340px;" onclick="get_aligmentywoprd2();">amount in word line two</div>
                        <div id="inWords3" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 47px;margin-top: 178px;display:none;border: 1px solid black;width: 340px;" onclick="get_aligmentywoprd3();">amount in words line three</div>
                        <div id="negotiable" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 194px;margin-top: 237px;" onclick="get_aligmentnego();"><img src="{{asset('chequer/img/Not-Negotiable.png')}}" style="width: 100%;"></div>
                        <div id="payonly" class="resizable draggable" contenteditable="true" style="position:absolute;color:black;margin-left: 50px;margin-top: 237px;" onclick="get_aligmentpayonly();"><img src="{{asset('chequer/img/pay-only.png')}}" style="width: 100%;"></div>
                        <div id="cross" class="resizable draggable" contenteditable="false" style="position:absolute;color:black;margin-left: 5px;margin-top: 7px;" onclick="get_aligmentcross();"><img src="{{asset('chequer/img/cross.png')}}" style="width: 100%;"></div>
                        <div id="indoubleCross" class="resizable draggable" contenteditable="false" style="position:absolute;color:black;margin-left: 5px;margin-top: 10px;display: none; width: 70px"  onclick="get_aligmentduble();"><img src="{{asset('chequer/img/cross.png')}}" style="width: 100%;"></div>
                        <div id="strikeBearer" class="resizable draggable" contenteditable="false" style="position:absolute;color:black;;display: none;" onclick="get_aligmentstrike();"><hr style="border: 1px solid #000;width: 100px;"></hr></div>
                        <div id="Stamp" class="resizable draggable" contenteditable="true" style="position:absolute;color:black; border: 1px solid black; height: 50px; width: 150px;display: none; display: none;" onclick="get_aligmentstamp();">Signature Stamp</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
</section>
@endsection
@php
    $temp_id = '';
@endphp
@section('javascript')
<script>
    $(".ruler[data-items]").each(function() {
        var ruler = $(this).empty(),
        len = 30,
        item = $(document.createElement("li")),
        i;
        for (i = 0; i < len; i++) {
            ruler.append(item.clone().text(i + 1));
        }
    });
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#addtemplateimg')[0].style.backgroundImage = "url(" + reader.result + ")";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $('.template_image').on('change', function(event) {
        readURL(this);
        var form = $("form#uplodafiles");
        var formData = new FormData(form[0]);
        $.ajax({
            type: "POST",
            url: $("form#uplodafiles").prop("action"),
            data: formData,
            contentType: false,
            processData: false,
        }).done(function(data) {
            console.log(data);
            
            if(data.success){
                toastr.success(data.msg);
                $("#image_url").val(data.filename);
            }else{
                toastr.error(data.msg);
            }
        });
    });
    
    //Copy template changes
    function copyTemplateChange(){
        $("#addtemplateimg").css("background-image", "url('')");
        var id = $('#copyTemplate :selected').val();
    
        $.ajax({
            url: '{{action("Chequer\ChequeTemplateController@getTemplateValues")}}',
            type: 'post',
            dataType: 'json',
            data: {id: id},
        }).done(function(data) {
            if(data.id){
                $("#tempid").val(data.id);
            }
    
            if(data.image_url){
              $('#addtemplateimg')[0].style.backgroundImage = "url({{url('/public/chequer')}}/uploads/" + data.image_url + ")";
            }
    
            if(data.template_name){
                $('#template_name').val(data.template_name);
                $('#seprator').val(data.seprator);
                if(data.words2 == 1){ 
                    $('#words2').prop('checked', true);
                    $('#inWords2').show();
                }else{
                    $('#words2').prop('checked', false);
                    $('#inWords2').hide();
                }
    
                if(data.words3 == 1){
                    $('#words3').prop('checked', true);
                    $('#inWords3').show();
                }else{
                    $('#words3').prop('checked', false);
                    $('#inWords3').hide();
                }
    
                if(data.is_dublecross == 1){
                    $('#doubleCross').prop('checked', true);
                    $('#indoubleCross').show();
                }else{
                    $('#doubleCross').prop('checked', false);
                    $('#indoubleCross').hide();
                }
    
                if(data.is_stamp == 1){
                    $('#StampCheckbox').prop('checked', true);
                    $('#Stamp').show();
                }else{
                    $('#StampCheckbox').prop('checked', false);
                    $('#Stamp').hide();
                }
    
                if(data.is_strikeBearer == 1){
                    $('#strikeBearerCheckbox').prop('checked', true);
                    $('#strikeBearer').show();
                    
                }else{
                    $('#strikeBearerCheckbox').prop('checked', false);
                    $('#strikeBearer').hide();
                }
    
                $('#dateformat').val(data.date_format);
    
                if(data.template_size){
                    var template_size = data.template_size.split(',');
                    var wa1=template_size[0].replace('px','');
                    var he1=template_size[1].replace('px','');
    
                    $('#template_width').val((wa1*0.0104166667).toFixed(2));
                    $('#template_height').val((he1*0.0104166667).toFixed(2));
                    $('#addtemplateimg').css('width',template_size[0]);
                    $('#addtemplateimg').css('height',template_size[1]);
                }
    
                if(data.pay_name){
                    var pay_name = data.pay_name.split(',');
                    $('#pay').css('margin-top', pay_name[0]);
                    $('#pay').css('margin-left', pay_name[1]);
                }
    
                if(data.date_pos){
                    var date_pos = data.date_pos.split(',');
                    $('#date').css('margin-top', date_pos[0]);
                    $('#date').css('margin-left', date_pos[1]);
                }
    
                if(data.amount){
                    var amount = data.amount.split(',');
                    $('#amount').css('margin-top', amount[0]);
                    $('#amount').css('margin-left', amount[1]);
                }
    
                if(data.amount_in_w1){
                    var amount_in_w1 = data.amount_in_w1.split(',');
                    $('#inWords1').css('margin-top', amount_in_w1[0]);
                    $('#inWords1').css('margin-left', amount_in_w1[1]);
                }
    
                if(data.amount_in_w2){
                    var amount_in_w2 = data.amount_in_w2.split(',');
                    $('#inWords2').css('margin-top', amount_in_w2[0]);
                    $('#inWords2').css('margin-left', amount_in_w2[1]);
                }
    
                if(data.amount_in_w3){
                    var amount_in_w3 = data.amount_in_w3.split(',');
                    $('#inWords3').css('margin-top', amount_in_w3[0]);
                    $('#inWords3').css('margin-left', amount_in_w3[1]);
                }
    
                if(data.not_negotiable){
                    var not_negotiable = data.not_negotiable.split(',');
                    $('#negotiable').css('margin-top', not_negotiable[0]);
                    $('#negotiable').css('margin-left', not_negotiable[1]);
                }
    
                if(data.pay_only){
                    var pay_only = data.pay_only.split(',');
                    $('#payonly').css('margin-top', pay_only[0]);
                    $('#payonly').css('margin-left', pay_only[1]);
                }
    
                if(data.template_cross){
                    var template_cross = data.template_cross.split(',');
                    $('#cross').css('margin-top', template_cross[0]);
                    $('#cross').css('margin-left', template_cross[1]);
                }
    
                if(data.signature_stamp){
                    var template_signature_stamp = data.signature_stamp.split(',');
                    var template_signature_stamp_area = data.signature_stamp_area.split(',');
    
                    $('#Stamp').css('margin-top', template_signature_stamp[0]);
                    $('#Stamp').css('margin-left', template_signature_stamp[1]);
                    $('#Stamp').css('height', template_signature_stamp_area[0]);
                    $('#Stamp').css('width', template_signature_stamp_area[1]);
                }
    
                if(data.strikeBearer){
                    var template_doublecross = data.dublecross.split(',');
                    $('#indoubleCross').css('margin-top', template_doublecross[0]);
                    $('#indoubleCross').css('margin-left', template_doublecross[1]);
                }
    
                if(data.strikeBearer){
                    var template_strikeBearer = data.strikeBearer.split(',');
                    $('#strikeBearer').css('margin-top', template_strikeBearer[0]);
                    $('#strikeBearer').css('margin-left', template_strikeBearer[1]);
                }
    
                if(data.d1){
                    var d1 = data.d1.split(',');
                    $('#d1').css('margin-top', d1[0]);
                    $('#d1').css('margin-left', d1[1]);
                }
    
                if(data.d2){
                    var d2 = data.d2.split(',');
                    $('#d2').css('margin-top', d2[0]);
                    $('#d2').css('margin-left', d2[1]);
                }
    
                if(data.m1){
                    var m1 = data.m1.split(',');
                    $('#m1').css('margin-top', m1[0]);
                    $('#m1').css('margin-left', m1[1]);
                }
                if(data.m2){
                    var m2 = data.m2.split(',');
                    $('#m2').css('margin-top', m2[0]);
                    $('#m2').css('margin-left', m2[1]);
                }
     
                var y1 = data.y1.split(',');
                if(y1=='0px,0px'){
                    $('#y1').hide();
                }else{
                    $('#y1').css('margin-top', y1[0]);
                    $('#y1').css('margin-left', y1[1]);
                }
    
                var y2 = data.y2.split(',');
                if(y2=='0px,0px'){
                    $('#y2').hide();
                }else{
                    $('#y2').css('margin-top', y2[0]);
                    $('#y2').css('margin-left', y2[1]);
                }
    
                var y3 = data.y3.split(',');
                $('#y3').css('margin-top', y3[0]);
                $('#y3').css('margin-left', y3[1]);
    
                var y4 = data.y4.split(',');
                $('#y4').css('margin-top', y4[0]);
                $('#y4').css('margin-left', y4[1]);
            }
        });
        datechange();
    }
    
    function saveTemplate(){
        $("#sendBtn").prop("disabled", true);
        var image_url = $('#image_url').val();
        var template_name = $('#template_name').val();
        var seprator = $('#seprator').val();
        var copyTemplate=$("#copyTemplate").val();
        var tw1 = $('#template_width').val();
        var tw=(tw1*96).toFixed();
        var temp_id=$("#tempid").val();
        var th1 = $('#template_height').val();
        var th=(th1*96).toFixed();
        var words2 = $('#words2:checked').length;
        var words3 = $('#words3:checked').length;
        var is_dublecross = $('#doubleCross:checked').length;
        var is_strikeBearer = $('#strikeBearerCheckbox:checked').length;
        var is_stamp = $('#StampCheckbox:checked').length;
        var template_size = tw + 'px,' + th + 'px';
        var pay_name = $('#pay').css('margin-top' ) + ',' +  $('#pay').css('margin-left' );
        var date_pos = $('.date_format').css('margin-top' ) + ',' +  $('.date_format').css('margin-left' );
        var date_format = $('#dateformat :selected').val();
        var amount = $('#amount').css('margin-top' ) + ',' +  $('#amount').css('margin-left' );
        var amount_in_words1 = $('#inWords1').css('margin-top' ) + ',' +  $('#inWords1').css('margin-left' ); 
        var amount_in_words2 = $('#inWords2').css('margin-top' ) + ',' +  $('#inWords2').css('margin-left' );
        var amount_in_words3 = $('#inWords3').css('margin-top' ) + ',' +  $('#inWords3').css('margin-left' );
        var cross = $('#cross').css('margin-top' ) + ',' +  $('#cross').css('margin-left' ); 
        var dublecross = $('#indoubleCross').css('margin-top' ) + ',' +  $('#cross').css('margin-left' ); 
        var pay_only = $('#payonly').css('margin-top' ) + ',' +  $('#payonly').css('margin-left' );
        var negotiable = $('#negotiable').css('margin-top' ) + ',' +  $('#negotiable').css('margin-left' );
        var signature_stamp = $('#Stamp').css('margin-top' ) + ',' +  $('#Stamp').css('margin-left' );
        var signature_stamp_area=$('#Stamp').css('height' ) + ',' +  $('#Stamp').css('width' );
        var strikeBearer = $('#strikeBearer').css('margin-top' ) + ',' +  $('#strikeBearer').css('margin-left' );
        var d1 = $('#d1').css('margin-top' ) + ',' +  $('#d1').css('margin-left' );
        var d2 = $('#d2').css('margin-top' ) + ',' +  $('#d2').css('margin-left' );
        var m1 = $('#m1').css('margin-top' ) + ',' +  $('#m1').css('margin-left' );
        var m2 = $('#m2').css('margin-top' ) + ',' +  $('#m2').css('margin-left' );
    
        var year1=$("#Y1").attr('class'); 
        var year2=$("#Y2").attr('class'); 
        if(year1=='digit_box digit_box2' && year2=='digit_box digit_box2'){
            var y1 = "0px" + ',' +  "0px";
            var y2 = "0px" + ',' +  "0px";
        }else{
            var y1 = $('#y1').css('margin-top' ) + ',' +  $('#y1').css('margin-left' );
            var y2 = $('#y2').css('margin-top' ) + ',' +  $('#y2').css('margin-left' ); 
        }
    
        var y3 = $('#y3').css('margin-top' ) + ',' +  $('#y3').css('margin-left' );
        var y4 = $('#y4').css('margin-top' ) + ',' +  $('#y4').css('margin-left' );
        var ds1 = $('#ds1').css('margin-top' ) + ',' +  $('#ds1').css('margin-left' );
        var ds2 = $('#ds2').css('margin-top' ) + ',' +  $('#ds2').css('margin-left' );
    
        $.ajax({
            url: '{{action("Chequer\ChequeTemplateController@store")}}',
            type: 'post',
            dataType: 'json',
            data: {image_url: image_url, template_name: template_name, template_size: template_size, pay_name: pay_name, date_pos: date_pos, date_format:date_format, amount: amount, amount_in_words1: amount_in_words1, amount_in_words2: amount_in_words2, amount_in_words3: amount_in_words3, cross: cross, pay_only: pay_only, negotiable: negotiable, signature_stamp: signature_stamp, seprator:seprator, words2:words2, words3:words3,dublecross:dublecross, is_dublecross:is_dublecross,strikeBearer:strikeBearer, is_stamp:is_stamp,is_strikeBearer:is_strikeBearer,d1:d1,d2:d2,m1:m1,m2:m2,y1:y1,y2:y2,y3:y3,y4:y4, ds1:ds1,ds2:ds2,signature_stamp_area:signature_stamp_area,copyTemplate:copyTemplate,temp_id:temp_id},
        }).done(function(data) {
            if(data.success){
                toastr.success(data.msg);
            }else{
                toastr.error(data.msg);
            }
            location.reload();
            /* window.location.replace(data.url); */
        });
    }
    </script>
    <script>
    function colorchange1() {
        var element = document.getElementById("D1");
        element.classList.toggle("digit_box2");
    }
    
    function colorchange2() {
        var element = document.getElementById("D2");
        element.classList.toggle("digit_box2");
    }
    
    function colorchange3() {
        var element = document.getElementById("M1");
        element.classList.toggle("digit_box2");
    }
        
    function colorchange4() {
        var element = document.getElementById("M2");
        element.classList.toggle("digit_box2");
    }
        
    function colorchange5() {
        var element = document.getElementById("Y1");
        element.classList.toggle("digit_box2");
    }
    
    function colorchange6() {
        var element = document.getElementById("Y2");
        element.classList.toggle("digit_box2");
    }
    
    function colorchange7() {
        var element = document.getElementById("Y3");
        element.classList.toggle("digit_box2");
    }
        
    function colorchange8() {
        var element = document.getElementById("Y4");
        element.classList.toggle("digit_box2");
    }
    
    $(document).ready(function(){
        $("#D1").click(function(){
            $("#d1").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#D2").click(function(){
            $("#d2").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#M1").click(function(){
            $("#m1").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#M2").click(function(){
            $("#m2").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#Y1").click(function(){
            $("#y1").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#Y2").click(function(){
            $("#y2").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#Y3").click(function(){
            $("#y3").toggle();
        });
    });
    
    $(document).ready(function(){
        $("#Y4").click(function(){
            $("#y4").toggle();
        });
    });
    
    function paymargin(){
        var px="px";
        var tw1 = document.getElementById("template_width").value;
        var tw=(tw1*96).toFixed();
        var th1 = document.getElementById("template_height").value;
        var th=(th1*96).toFixed();
    
        document.getElementById("addtemplateimg").style.height=th+px ;
        document.getElementById("addtemplateimg").style.width=tw+px ;
    }
    
    function doubleCrosses() {
        var checkBox = document.getElementById("doubleCross");
        if (checkBox.checked == true){
            $("#indoubleCross").show();
        } else {
           $("#indoubleCross").hide();
        }
    }
    
    function Stamp() {
        var checkBox = document.getElementById("StampCheckbox");
        if (checkBox.checked == true){
            $("#Stamp").show();
        } else {
           $("#Stamp").hide();
        }
    }
    
    function strikeBearer() {
        var checkBox = document.getElementById("strikeBearerCheckbox");
        if (checkBox.checked == true){
            $("#strikeBearer").show();
        } else {
           $("#strikeBearer").hide();
        }
        console.log('asdf');
        
    }
    
    function words() {
        var checkBox = document.getElementById("words2");
        var text = document.getElementById("inWords2");
        if (checkBox.checked == true){
            inWords2.style.display = "block";
        } else {
           inWords2.style.display = "none";
        }
        var checkBox = document.getElementById("words3");
        var text = document.getElementById("inWords3");
        if (checkBox.checked == true){
            inWords3.style.display = "block";
        } else {
            inWords3.style.display = "none";
        }
    }
    
    function datechange(){
        var df = document.getElementById("dateformat").value;
        var ds = document.getElementById("seprator").value;
        if(df=== "dd-mm-yyyy"){
            document.getElementById("d1").innerHTML='d';
            document.getElementById("d2").innerHTML='d';
            document.getElementById("m1").innerHTML='m';
            document.getElementById("m2").innerHTML='m';
            document.getElementById("y1").innerHTML='y';
            document.getElementById("y2").innerHTML='y';
            document.getElementById("y3").innerHTML='y';
            document.getElementById("y4").innerHTML='y';
            document.getElementById("D1").innerHTML='d';
            document.getElementById("D2").innerHTML='d';
            document.getElementById("M1").innerHTML='m';
            document.getElementById("M2").innerHTML='m';
            document.getElementById("Y1").innerHTML='y';
            document.getElementById("Y2").innerHTML='y';
            document.getElementById("Y3").innerHTML='y';
            document.getElementById("Y4").innerHTML='y';
        }
    
        if(df=== "mm-dd-yyyy"){
            document.getElementById("d1").innerHTML='m';
            document.getElementById("d2").innerHTML='m';
            document.getElementById("m1").innerHTML='d';
            document.getElementById("m2").innerHTML='d';
            document.getElementById("y1").innerHTML='y';
            document.getElementById("y2").innerHTML='y';
            document.getElementById("y3").innerHTML='y';
            document.getElementById("y4").innerHTML='y';
            document.getElementById("D1").innerHTML='m';
            document.getElementById("D2").innerHTML='m';
            document.getElementById("M1").innerHTML='d';
            document.getElementById("M2").innerHTML='d';
            document.getElementById("Y1").innerHTML='y';
            document.getElementById("Y2").innerHTML='y';
            document.getElementById("Y3").innerHTML='y';
            document.getElementById("Y4").innerHTML='y';
        }
    
        if(df=== "yyyy-mm-dd"){
            document.getElementById("d1").innerHTML='y';
            document.getElementById("d2").innerHTML='y';
            document.getElementById("m1").innerHTML='y';
            document.getElementById("m2").innerHTML='y';
            document.getElementById("y1").innerHTML='m';
            document.getElementById("y2").innerHTML='m';
            document.getElementById("y3").innerHTML='d';
            document.getElementById("y4").innerHTML='d';
            document.getElementById("D1").innerHTML='y';
            document.getElementById("D2").innerHTML='y';
            document.getElementById("M1").innerHTML='y';
            document.getElementById("M2").innerHTML='y';
            document.getElementById("Y1").innerHTML='m';
            document.getElementById("Y2").innerHTML='m';
            document.getElementById("Y3").innerHTML='d';
            document.getElementById("Y4").innerHTML='d';
        }
    }
    </script>
    <script>
    var dragging = false,
    currentDragged;
    var resizeHandles ='<div class="resize nw" id="nw" draggable="false" contenteditable="false"></div><div class="resize n" id="n" draggable="false" contenteditable="false"></div><div class="resize ne" id="ne" draggable="false" contenteditable="false"></div><div class="resize w" id="w" draggable="false" contenteditable="false"></div><div class="resize e" id="e" draggable="false" contenteditable="false"></div><div class="resize sw" id="sw" draggable="false" contenteditable="false"></div><div class="resize s" id="s" draggable="false" contenteditable="false"></div><div class="resize se" id="se" draggable="false" contenteditable="false"></div>';
    var resizing = false,
    currentResizeHandle,
    sX,
    sY;
    
    var mousedownEventType = ((document.ontouchstart!==null)?'mousedown':'touchstart'),
    mousemoveEventType = ((document.ontouchmove!==null)?'mousemove':'touchmove'),
    mouseupEventType = ((document.ontouchmove!==null)?'mouseup':'touchend');
    
    $('.draggable').on(mousedownEventType, function(e){
        if (!e.target.classList.contains("resize") && !resizing) {
            currentDragged = $(this);
            dragging = true;
            sX = e.pageX;
            sY = e.pageY;
        }
    });
    
    $(".resizable").focus(function(e){
        $(this).html($(this).html() + resizeHandles);
        $(".resize").on(mousedownEventType, function(e){
            currentResizeHandle = $(this);
            resizing = true;
            sX = e.pageX;
            sY = e.pageY;
        });
    }).blur(function(e){
        $(this).children(".resize").remove();
    });
    
    $("body").on(mousemoveEventType, function(e) {
        var xChange = e.pageX - sX,
        yChange = e.pageY - sY;
        if (resizing) {
            e.preventDefault();
            var parent  = currentResizeHandle.parent();
            switch (currentResizeHandle.attr('id')) {
                case "nw":
                    var newWidth = parseFloat(parent.css('width')) - xChange,
                    newHeight = parseFloat(parent.css('height')) - yChange,
                    newLeft = parseFloat(parent.css('margin-left')) + xChange,
                    newTop = parseFloat(parent.css('margin-top')) + yChange;
                break;
                case "n":
                    var newWidth = parseFloat(parent.css('width')),
                    newHeight = parseFloat(parent.css('height')) - yChange,
                    newLeft = parseFloat(parent.css('margin-left')),
                    newTop = parseFloat(parent.css('margin-top')) + yChange;
                break;
                case "ne":
                    var newWidth = parseFloat(parent.css('width')) + xChange,
                    newHeight = parseFloat(parent.css('height')) - yChange,
                    newLeft = parseFloat(parent.css('margin-left')),
                    newTop = parseFloat(parent.css('margin-top')) + yChange;
                break;
                case "e":
                    var newWidth = parseFloat(parent.css('width')) + xChange,
                    newHeight = parseFloat(parent.css('height')),
                    newLeft = parseFloat(parent.css('margin-left')),
                    newTop = parseFloat(parent.css('margin-top'));
                break;
                case "w":
                    var newWidth = parseFloat(parent.css('width')) - xChange,
                    newHeight = parseFloat(parent.css('height')),
                    newLeft = parseFloat(parent.css('margin-left')) + xChange,
                    newTop = parseFloat(parent.css('margin-top'));
                break;
                case "sw":
                    var newWidth = parseFloat(parent.css('width')) - xChange,
                    newHeight = parseFloat(parent.css('height')) + yChange,
                    newLeft = parseFloat(parent.css('margin-left')) + xChange,
                    newTop = parseFloat(parent.css('margin-top'));
                break;
                case "s":
                    var newWidth = parseFloat(parent.css('width')),
                    newHeight = parseFloat(parent.css('height')) + yChange,
                    newLeft = parseFloat(parent.css('margin-left')),
                    newTop = parseFloat(parent.css('margin-top'));
                break;
                case "se":
                    var newWidth = parseFloat(parent.css('width')) + xChange,
                    newHeight = parseFloat(parent.css('height')) + yChange,
                    newLeft = parseFloat(parent.css('margin-left')),
                    newTop = parseFloat(parent.css('margin-top'));
                break;
            }
    
            var containerWidth = parseFloat(parent.parent().css("width"));
            if (newLeft < 0) {
                newWidth += newLeft;
                newLeft = 0;
            }
    
            if (newWidth < 0) {
                newWidth = 0;
                newLeft = parent.css("margin-left");
            }
    
            if (newLeft + newWidth > containerWidth) {
                newWidth = containerWidth-newLeft;
            }
    
            parent.css('margin-left', newLeft + "px").css('width', newWidth + "px");
            sX = e.pageX;
    
            //Height
            var containerHeight = parseFloat(parent.parent().css("height"));
            if (newTop < 0) {
                newHeight += newTop;
                newTop = 0;
            }
            if (newHeight < 0) {
                newHeight = 0;
                newTop = parent.css("margin-top");
            }
            if (newTop + newHeight > containerHeight) {
                newHeight = containerHeight-newTop;
            }
    
            parent.css('margin-top', newTop + "px").css('height', newHeight + "px");
            sY = e.pageY;
        } else if (dragging) {
            e.preventDefault();
            var draggedWidth = parseFloat(currentDragged.css("width")),
            draggedHeight = parseFloat(currentDragged.css("height")),
            containerWidth = parseFloat(currentDragged.parent().css("width")),
            containerHeight = parseFloat(currentDragged.parent().css("height"));
    
            var newLeft = (parseFloat(currentDragged.css("margin-left")) + xChange),
            newTop = (parseFloat(currentDragged.css("margin-top")) + yChange);
    
            if (newLeft < 0) {
                newLeft = 0;
            }
            if (newTop < 0) {
                newTop = 0;
            }
            if (newLeft + draggedWidth > containerWidth) {
                newLeft = containerWidth - draggedWidth;
            }
            if (newTop + draggedHeight > containerHeight) {
                newTop = containerHeight - draggedHeight;
            }
    
            currentDragged.css("margin-left", newLeft + "px").css("margin-top", newTop + "px");
            sX = e.pageX;
            sY = e.pageY;
        }
    }).on(mouseupEventType, function(e){
        dragging = false;
        resizing = false;
    });
    </script>
    <script>
    $(document).ready(function(){
        var tempid = '@if(isset($id)){{$id}}@endif';
        if(tempid != ''){
            $('#copyTemplate').val(tempid);
            copyTemplateChange();
            $('.copyTemplateClass').hide();
            $('.page-header').html('Update Template');
        }
    });
    </script>
    <script>
    function get_aligmentd1(){
        var d1top = $('#d1').css('margin-top');
        var d1left=  $('#d1').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('d1');
    }
    
    function get_aligmentd2(){
        var d1top = $('#d2').css('margin-top');
        var d1left=  $('#d2').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('d2');
    }
    
    function get_aligmentm1(){
        var d1top = $('#m1').css('margin-top');
        var d1left=  $('#m1').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('m1');
    }
    
    function get_aligmentm2(){
        var d1top = $('#m2').css('margin-top');
        var d1left=  $('#m2').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('m2');
    }
    
    function get_aligmenty1(){
        var d1top = $('#y1').css('margin-top');
        var d1left=  $('#y1').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('y1');
    }
    
    function get_aligmenty2(){
        var d1top = $('#y2').css('margin-top');
        var d1left=  $('#y2').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('y2');
    }
    
    function get_aligmenty3(){
        var d1top = $('#y3').css('margin-top');
        var d1left=  $('#y3').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('y3');
    }
    
    function get_aligmenty4(){
        var d1top = $('#y4').css('margin-top');
        var d1left=  $('#y4').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('y4');
    }
    
    function get_aligmentypay(){
        var d1top = $('#pay').css('margin-top');
        var d1left=  $('#pay').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('pay');
    }
    
    function get_aligmentyamount(){
        var d1top = $('#amount').css('margin-top');
        var d1left=  $('#amount').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('amount'); 
    }
    
    function get_aligmentnego(){
        var d1top = $('#negotiable').css('margin-top');
        var d1left=  $('#negotiable').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('negotiable'); 
    }
    
    function get_aligmentywoprd1(){
        var d1top = $('#inWords1').css('margin-top');
        var d1left=  $('#inWords1').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('inWords1'); 
    }
    
    function get_aligmentywoprd2(){
        var d1top = $('#inWords2').css('margin-top');
        var d1left=  $('#inWords2').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('inWords2'); 
    }
    
    function get_aligmentywoprd3(){
        var d1top = $('#inWords3').css('margin-top');
        var d1left=  $('#inWords3').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('inWords3'); 
    }
    
    function get_aligmentpayonly(){
        var d1top = $('#payonly').css('margin-top');
        var d1left=  $('#payonly').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('payonly'); 
    }
    
    function get_aligmentstrike(){
        var d1top = $('#strikeBearer').css('margin-top');
        var d1left=  $('#strikeBearer').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('strikeBearer'); 
    }
    
    function get_aligmentstamp(){
        var d1top = $('#Stamp').css('margin-top');
        var d1left=  $('#Stamp').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('Stamp'); 
    }
    
    function get_aligmentcross(){
        var d1top = $('#cross').css('margin-top');
        var d1left=  $('#cross').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('cross'); 
    }
    
    function get_aligmentduble(){
        var d1top = $('#indoubleCross').css('margin-top');
        var d1left=  $('#indoubleCross').css('margin-left');
        var myString1 = (d1top.replace(/\D/g,'')*0.0104166667).toFixed(2);
        var myString2 = (d1left.replace(/\D/g,'')*0.0104166667).toFixed(2);
    
        $("#move_left").val(myString2);
        $("#move_top").val(myString1);
        $("#div_id").val('indoubleCross'); 
    }
    
    function datemargin(){
        var myString1= $("#move_left").val();
        var myString2=$("#move_top").val();
        var div_id= $("#div_id").val();
        var move_top = (myString2*96).toFixed();
        var move_left = (myString1*96).toFixed();
        if(div_id=='d1'){
            $('#d1').css('margin-top', move_top+ "px");  
            $('#d1').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='d2'){
            $('#d2').css('margin-top', move_top+ "px");  
            $('#d2').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='m1'){
            $('#m1').css('margin-top', move_top+ "px");  
            $('#m1').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='m2'){
            $('#m2').css('margin-top', move_top+ "px");  
            $('#m2').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='y1'){
            $('#y1').css('margin-top', move_top+ "px");  
            $('#y1').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='y2'){
            $('#y2').css('margin-top', move_top+ "px");  
            $('#y2').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='y3'){
            $('#y3').css('margin-top', move_top+ "px");  
            $('#y3').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='y4'){
            $('#y4').css('margin-top', move_top+ "px");  
            $('#y4').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='pay'){
            $('#pay').css('margin-top', move_top+ "px");  
            $('#pay').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='amount'){
            $('#amount').css('margin-top', move_top+ "px");  
            $('#amount').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='inWords1'){
            $('#inWords1').css('margin-top', move_top+ "px");  
            $('#inWords1').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='inWords2'){
            $('#inWords2').css('margin-top', move_top+ "px");  
            $('#inWords2').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='inWords3'){
            $('#inWords3').css('margin-top', move_top+ "px");  
            $('#inWords3').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='negotiable'){
            $('#negotiable').css('margin-top', move_top+ "px");  
            $('#negotiable').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='payonly'){
            $('#payonly').css('margin-top', move_top+ "px");  
            $('#payonly').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='cross'){
            $('#cross').css('margin-top', move_top+ "px");  
            $('#cross').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='Stamp'){
            $('#Stamp').css('margin-top', move_top+ "px");  
            $('#Stamp').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='strikeBearer'){
            $('#strikeBearer').css('margin-top', move_top+ "px");  
            $('#strikeBearer').css('margin-left', move_left+ "px");
        }
    
        if(div_id=='indoubleCross'){
            $('#indoubleCross').css('margin-top', move_top+ "px");  
            $('#indoubleCross').css('margin-left', move_left+ "px");
        }
    }
    </script>
@endsection