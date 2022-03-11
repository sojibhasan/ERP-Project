<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'report.stock_adjustment_report' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="stock_adjustment_location_filter" style="width: 100%;">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="stock_adjustment_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-6">
            @component('components.widget')
                <table class="table no-border">
                    <tr>
                        <th>{{ __('report.total_normal') }}:</th>
                        <td>
                            <span class="total_normal">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.total_abnormal') }}:</th>
                        <td>
                             <span class="total_abnormal">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.total_stock_adjustment') }}:</th>
                        <td>
                            <span class="total_amount">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </table>
            @endcomponent
        </div>

        <div class="col-sm-6">
            @component('components.widget')
                <table class="table no-border">
                    <tr>
                        <th>{{ __('report.total_recovered') }}:</th>
                        <td>
                             <span class="total_recovered">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                </table>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('stock_adjustment.stock_adjustments')])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="stock_adjustment_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="notexport">@lang('messages.action')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('business.location')</th>
                                <th>@lang('stock_adjustment.adjustment_type')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                <th>@lang('stock_adjustment.total_amount')</th>
                                <th>@lang('stock_adjustment.total_amount_recovered')</th>
                                <th>@lang('purchase.additional_notes')</th>
                                <th>@lang('stock_adjustment.reason_for_stock_adjustment')</th>
                                <th>@lang('lang_v1.added_by')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
	

</section>
<!-- /.content -->
