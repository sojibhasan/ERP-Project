<script type="text/javascript">
    base_path = "{{url('/')}}";
</script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js?v=$asset_v"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=$asset_v"></script>
<![endif]-->

<!-- jQuery 2.2.3 -->
<script src="{{ asset('AdminLTE/plugins/jQuery/jquery-2.2.3.min.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js?v=' . $asset_v) }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('bootstrap/js/bootstrap.min.js?v=' . $asset_v) }}"></script>
<!-- iCheck -->
<script src="{{ asset('AdminLTE/plugins/iCheck/icheck.min.js?v=' . $asset_v) }}"></script>
<!-- jQuery Step -->
<script src="{{ asset('plugins/jquery.steps/jquery.steps.min.js?v=' . $asset_v) }}"></script>
<!-- Select2 -->
<script src="{{ asset('AdminLTE/plugins/select2/select2.full.min.js?v=' . $asset_v) }}"></script>
<!-- Add language file for select2 -->
@if(file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) .
'.js')))
<script
    src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}">
</script>
@endif

<!-- bootstrap toggle -->
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!-- bootstrap datepicker -->
<script src="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.js?v=' . $asset_v) }}"></script>
<!-- DataTables -->
<script src="{{ asset('AdminLTE/plugins/DataTables/datatables.min.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/pdfmake.min.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/vfs_fonts.js?v=' . $asset_v) }}"></script>

<!-- jQuery Validator -->
<script src="{{ asset('js/jquery-validation-1.16.0/dist/jquery.validate.min.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/jquery-validation-1.16.0/dist/additional-methods.min.js?v=' . $asset_v) }}"></script>
@php
$validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale') ) . '.js';
@endphp
@if(file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
<script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}">
</script>
@endif

<!-- Toastr -->
<script src="{{ asset('plugins/toastr/toastr.min.js?v=' . $asset_v) }}"></script>
<!-- Bootstrap file input -->
<script src="{{ asset('plugins/bootstrap-fileinput/fileinput.min.js?v=' . $asset_v) }}"></script>
<!--accounting js-->
<script src="{{ asset('plugins/accounting.min.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('AdminLTE/plugins/daterangepicker/moment.min.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('AdminLTE/plugins/ckeditor/ckeditor.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('plugins/sweetalert/sweetalert.min.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('plugins/bootstrap-tour/bootstrap-tour.min.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('plugins/printThis.js?v=' . $asset_v) }}"></script>

<script src="{{ asset('plugins/screenfull.min.js?v=' . $asset_v) }}""></script>

<script src=" {{ asset('plugins/moment-timezone-with-data.min.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/offline.js') }}"></script>
{{-- <script type="module"  src="{{ asset('js/colorpicker/Colorpicker.js') }}"></script> --}}
<script src="{{asset('js/pickr.min.js') }}"></script>
@php
$business_date_format = session('business.date_format', config('constants.default_date_format'));
$datepicker_date_format = str_replace('d', 'dd', $business_date_format);
$datepicker_date_format = str_replace('m', 'mm', $datepicker_date_format);
$datepicker_date_format = str_replace('Y', 'yyyy', $datepicker_date_format);

$moment_date_format = str_replace('d', 'DD', $business_date_format);
$moment_date_format = str_replace('m', 'MM', $moment_date_format);
$moment_date_format = str_replace('Y', 'YYYY', $moment_date_format);

$business_time_format = session('business.time_format');
$moment_time_format = 'HH:mm';
if($business_time_format == 12){
$moment_time_format = 'hh:mm A';
}

$common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

$default_datatable_page_entries = !empty($common_settings['default_datatable_page_entries']) ?
$common_settings['default_datatable_page_entries'] : 25;

if(\Auth::user()){
    $user_id = \Auth::user()->id;
}

@endphp
<script>
    moment.tz.setDefault('{{ Session::get("business.time_zone") }}');
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        @if(config('app.debug') == false)
            $.fn.dataTable.ext.errMode = 'throw';
        @endif
    });
    
    var financial_year = {
    	start: moment('{{ Session::get("financial_year.start") }}'),
    	end: moment('{{ Session::get("financial_year.end") }}'),
    }
    @if(file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    //Default setting for select2
    $.fn.select2.defaults.set("language", "{{session()->get('user.language', config('app.locale'))}}");
    @endif

    var datepicker_date_format = @if(!empty($datepicker_date_format))  "{{$datepicker_date_format}}" @else "mm/dd/yyyy" @endif;
    var moment_date_format = @if(!empty($moment_date_format))  "{{$moment_date_format}}" @else "MM/DD/YYYY" @endif;
    var moment_time_format = @if(!empty($moment_time_format))  "{{$moment_time_format}}" @else "HH:mm" @endif;

    var app_locale = "{{session()->get('user.language', config('app.locale'))}}";
    var non_utf8_languages = [
        @foreach(config('constants.non_utf8_languages') as $const)
        "{{$const}}",
        @endforeach
    ];

    var __default_datatable_page_entries = "{{$default_datatable_page_entries}}";
</script>

<!-- Scripts -->
@inject('request', 'Illuminate\Http\Request')
@if ($request->segment(1) != 'login')
<script src="{{ asset('js/AdminLTE-app.js?v=' . $asset_v) }}"></script>
@endif


@if(file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
<script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}">
</script>
@else
<script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif
{{-- @if(request()->segment(count(request()->segments())) != 'login') --}}
<script src="{{ asset('AdminLTE/plugins/pace/pace.min.js?v=' . $asset_v) }}"></script>
{{-- @endif --}}


<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/app.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/help-tour.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('plugins/calculator/calculator.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/documents_and_note.js?v=' . $asset_v) }}"></script>

<script src="https://js.pusher.com/6.0/pusher.min.js">
</script>
@auth
<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('60edfb46c1105e962a07', {
    cluster: 'eu'
    });

    var channel = pusher.subscribe('customer-limit-approval-channel.{{auth()->user()->id}}');
    channel.bind('App\\Events\\CustomerLimitApproval', function(data) {
        $('ul#notifications_list').prepend(`
        <li class="">
        <a class="request-approval-link" href="/customer-limit-approval/get-approval-details/${data.customer_id}/${data.requested_user}">
            <i class=""></i> Request for over sell limit approval <br> Customer: ${data.customer_name}   <br>
            <small>${data.created_at}</small>
        </a>
        </li>
        `);

        let notification_count = $('.notifications_count').text();

        if(notification_count === ''){
            console.log('asdf');
            notification_count = 1;
        }else{
            notification_count = parseInt(notification_count) + 1;
        }
        $('.notifications_count').text(notification_count);
        toastr.info('New request received');
        pusher.disconnect();
    });

    $(document).on('click', 'a.request-approval-link', function(e){
        e.preventDefault();
        $.ajax({
            method: 'get',
            url: $(this).attr('href'),
            data: {  },
            success: function(result) {
                $('.limit_modal').empty().append(result);
                $('.limit_modal').modal('show');
            },
        });
    });

    $(document).on('click', '#limit_form_btn',function(e){
        e.preventDefault();

        $.ajax({
            method: 'post',
            url: $('#limit_form').attr('action'),
            data: { over_limit_percentage : $('#over_limit_percentage').val() , requested_user : $('#requested_user').val() },
            success: function(result) {
                if(result.success === 1){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
                $('.limit_modal').modal('hide');
            },
        });
    });


    var channel_apprved = pusher.subscribe('customer-limit-approved.{{auth()->user()->id}}');
    channel_apprved.bind('App\\Events\\CustomerLimitApproved', function(data) {
        toastr.success(`Sell over limit approved upto ${data.limit}% for customer ${data.customer_name}`);
        pusher.disconnect();
    });

    var stock_transfer_channel = pusher.subscribe('stock-transfer-request-complete.{{auth()->user()->id}}');
    stock_transfer_channel.bind('App\\Events\\StockTransferRequestComplete', function(data) {
        $('ul#notifications_list').prepend(`
        <li class="">
        <a class="stock-transfer-request-link" href="/stock-transfers-request/get-notification-poup/${data.transfer_request.id}">
            <i class=""></i> Request for  <br> Product: ${data.product.name}    <br>
            <i>Status: ${data.transfer_request.status} </i>
            <small>${data.transfer_request.updated_at}</small>
        </a>
        </li>
        `);

        let notification_count = $('.notifications_count').text();

        if(notification_count === ''){
            console.log('asdf');
            notification_count = 1;
        }else{
            notification_count = parseInt(notification_count) + 1;
        }
        
        $('.notifications_count').text(notification_count);
        toastr.info('New notification received');
        pusher.disconnect();
    });

    $(document).on('click', 'a.stock-transfer-request-link', function(e){
        e.preventDefault();
        $.ajax({
            method: 'get',
            url: $(this).attr('href'),
            data: {  },
            success: function(result) {
                $('.stock_tranfer_notification_model').empty().append(result);
            },
        });
    });

    
    $('.clear_cache_btn').click(function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        $.ajax({
            method: 'get',
            url: url,
            data: {  },
            success: function(result) {
                if(result.success){
                    toastr.success(result.msg);
                }else{
                    toastr.error(result.msg);
                }
            },
        });  
    });
    
    $(document).on('click', 'a#login_payroll', function(e){
        $('.loading_gif').show();
        var user_id = <?php echo $user_id; ?>;
        e.preventDefault();
        $.ajax({
            method: 'post',
            url: '/home/login_payroll',
            data: { user_id },
            success: function(result) {
                $('.loading_gif').hide();
                window.open(result.login_url, "_blank")
            },
        });
    });
</script>
<div class="modal fade limit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endauth


@yield('javascript')

@if(Module::has('Essentials'))
@includeIf('essentials::layouts.partials.footer_part')
@endif