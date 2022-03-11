@extends('layouts.app')
@section('title', __( 'account.balance_sheet' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.balance_sheet')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-sm-12">
            <div class="col-md-3 col-xs-6">
                <label for="business_location">@lang('account.business_locations'):</label>
                {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'business_location']) !!}
            </div>
            <div class="col-sm-3 col-xs-6 pull-right">
                <label for="end_date">@lang('messages.filter_by_date'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="end_date" value="{{@format_date('now')}}" class="form-control" readonly>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="box box-solid">
        <div class="box-header text-center">
            <h2 class="box-title">{{session()->get('business.name')}} - @lang( 'account.balance_sheet') - <span
                    id="hidden_date">{{@format_date('now')}}</span></h2>
            @if(!$account_access)
            <div class="text-center"
                style="color: {{App\System::getProperty('not_enalbed_module_user_color')}}; font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;">
                <p colspan="2"> {{App\System::getProperty('not_enalbed_module_user_message')}}</p>
            </div>
            @endif
        </div>
        <div class="box-body" style="width: 100%;">
            
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-primary no-print pull-right" onclick="window.print()">
                <i class="fa fa-print"></i> @lang('messages.print')</button>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        //Date picker
        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        update_balance_sheet();

        $('#end_date, #business_location').change( function() {
            update_balance_sheet();
            $('#hidden_date').text($(this).val());
        });
    });

    function update_balance_sheet(){
        var loader = '<div style="width:100%; text-align: center" class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
        $('div.box-body').each( function() {
            $(this).html(loader);
        });
        var end_date = $('input#end_date').val();
        var location_id = $('select#business_location').val();
        $.ajax({
            url: "{{action('AccountReportsController@balanceSheet')}}?end_date=" + end_date + "&location_id=" + location_id,
            contentType: 'html',
            success: function(result){
               $('.box-body').empty().append(result);
                
            }
        });
    }
</script>

@endsection