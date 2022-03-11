@extends('layouts.app')
@section('title', __('mpcs::lang.F14BAnd20_form'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#f14b_form_tab" class="f14b_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f14b_form')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#f20_form_tab" class="f20_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f20_form')</strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="f14b_form_tab">
                        @include('mpcs::forms.partials.f14b_form')
                    </div>
                    <div class="tab-pane" id="f20_form_tab">
                        @include('mpcs::forms.partials.f20_form')
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
 $('#f14b_date_range').daterangepicker();
    if ($('#f20_date_range').length == 1) {
        $('#f20_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#f20_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#f20_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#f20_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#f20_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    

   
</script>
@endsection