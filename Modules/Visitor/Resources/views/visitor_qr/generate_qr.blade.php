@extends('layouts.app')
@section('title', __( 'visitor::lang.qr_visitor_reg' ))
<style>
    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
    .toggle.ios .toggle-handle { border-radius: 20px; }
</style>
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'visitor::lang.qr_visitor_reg' )</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])

            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location').':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control', 'placeholder' =>
                __('messages.please_select')]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('color', __('productcatalogue::lang.qr_code_color').':') !!}
                {!! Form::text('color', '#000000', ['class' => 'form-control']); !!}
                <br>
                {!! Form::text('color-picker', null, ['class' => 'form-control color-picker', 'id' =>
                'color-picker', 'placeholder' => __( 'tasksmanagement::lang.color' )]);
                !!}
            </div>
            <div class="form-group">
                <label class="checkbox-inline">
                    <input type="checkbox" name="next_time" id="next_time_toggle" checked data-toggle="toggle"> @lang('lang_v1.save_for_next_time')
                </label>
            </div>
            <br>
            <button type="button" class="btn btn-primary" id="generate_qr">@lang( 'productcatalogue::lang.generate_qr'
                )</button>



            @endcomponent
        </div>

        <div class="col-md-5">
            @component('components.widget', ['class' => 'box-solid'])

            <div class="text-center">
                <div id="canvas_div" class="@if(empty($visitor_qr_data)) hide @endif" style="   width: 300px; margin: auto;">
                    <img src="{{$visitor_qr_data}}" id="qr_img" class="@if(empty($visitor_qr_data)) hide @endif">
                    <br>
                    <span
                        style="margin-top: 0px; padding-top: 0px;">{{App\System::getProperty('admin_msg_visitor_qr')}}</span>
                </div>
                <br><span id="visitor_qr_link"></span>
                <br>
                <a href="{{$visitor_qr_data}}" class="btn btn-success @if(empty($visitor_qr_data)) hide @endif" id="download_image" target="_blank"
                    download="QRCode.png">@lang( 'productcatalogue::lang.download_image' )</a>
            </div>
            @endcomponent
        </div>


    </div>
</section>
@stop
@section('javascript')
<script src="{{ asset('js/html2canvas.js') }}"></script>
<script src="{{ asset('modules/productcatalogue/plugins/qrcode/qrcode.js') }}"></script>
<script type="text/javascript">
    $('#next_time_toggle').bootstrapToggle({
      on: 'On',
      off: 'Off',
      size: 'small'
    });
    $(document).ready( function(){


    const pickr = Pickr.create({
    el: '.color-picker',
    theme: 'classic', // or 'monolith', or 'nano'
    default: '#03C74D',
    position: 'top-middle',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    components: {

        // Main components
        preview: true,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: true,
            input: true,
            clear: true,
            save: true,
            useAsButton: false,
        }
    }
  }).on('save', (color, instance) => {
      $('#color').val(color.toHEXA().toString());
  });

    });
    $('#generate_qr').click(function(){
        if ($('#location_id').val()) {
          
            var link = "{{url('visitor/' . session('business.id'))}}/" + $('#location_id').val() ;
            var color = '#000000';
            if ($('#color').val().trim() != '') {
                color = $('#color').val();
            }
            var opts = {
                errorCorrectionLevel: 'H',
                margin: 4,
                width: 250,
                color: {
                    dark: color,
                    light: "#ffffffff"
                }
            }

            var canvas_img_url =  null ;
            html2canvas(document.querySelector("#canvas_div")).then(canvas => {
                canvas.id = "h2canvas";
                $('#canvas_div').empty().append(canvas);
                var canvas_img_url    = canvas.toDataURL();
                
                $('#download_image').attr('href', canvas_img_url);

                if($('#next_time_toggle').prop('checked')){
                    $.ajax({
                        method: 'post',
                        url: '/visitor-module/qr-visitor-save',
                        data: { 
                            visitor_qr_data : canvas_img_url
                        },
                        success: function(result) {
                            console.log(result);
                        },
                    });
                }
            });
            
            QRCode.toDataURL(link, opts, function (err, url){
                $('#qr_img').attr('src', url);
                $('#qr_img').removeClass('hide');
                $('#visitor_qr_link').html('<a target="_blank" href="'+ link +'">Link</a>');
                $('#download_image').removeClass('hide');
                $('#canvas_div').removeClass('hide');
            })
               


            
        } else {
            alert("{{__('productcatalogue::lang.select_business_location')}}")
        }
    });

    $('select[name=location_id] option:eq(1)').attr('selected', 'selected').trigger('change');
</script>
@endsection