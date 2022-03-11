<!-- Main content -->
<section class="content">
    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang( 'manufacturing::lang.manufacturing_report' )</h2></div>
    
    <div class="row no-print">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="mfg_report_location_filter" style="width: 100%;">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="mfg_report_date_filter">
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
        <div class="col-xs-6">
            @component('components.widget')
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('manufacturing::lang.total_production') }}:</th>
                        <td>
                            <span class="total_production">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr> 
                    <tr>
                        <th>{{ __('manufacturing::lang.total_production_cost') }}:</th>
                        <td>
                            <span class="total_production_cost">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>      
                </table>
            @endcomponent
        </div>

        <div class="col-xs-6">
            @component('components.widget')
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('lang_v1.total_sold') }}:</th>
                        <td>
                            <span class="total_sold">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </table>
            @endcomponent
        </div>
    </div>
    <br>
    <div class="row no-print">
        <div class="col-md-3">
            <a href="{{action('ReportController@getStockReport')}}?only_mfg=true" class="btn btn-info btn-flat btn-block">@lang('report.stock_report')</a>
        </div>
        @if(session('business.enable_lot_number') == 1)
        <div class="col-md-3">
            <a href="{{action('ReportController@getLotReport')}}?only_mfg=true" class="btn btn-success btn-flat btn-block">@lang('lang_v1.lot_report')</a>
        </div>
        @endif
        @if(session('business.enable_product_expiry') == 1)
        <div class="col-md-3">
            <a href="{{action('ReportController@getStockExpiryReport')}}?only_mfg=true" class="btn btn-warning btn-flat btn-block">@lang('report.stock_expiry_report')</a>
        </div>
        @endif
        <div class="col-md-3">
            <a href="{{action('ReportController@itemsReport')}}?only_mfg=true" class="btn btn-danger btn-flat btn-block">@lang('lang_v1.items_report')</a>
        </div>
    </div>
    <br>
    <div class="row no-print">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right" 
            aria-label="Print" onclick="window.print();"
            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button>
        </div>
    </div>
	

</section>
