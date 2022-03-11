
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
         
             <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('20_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('20_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_20_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_20_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.F20_from_no') . ':') !!}
                    {!! Form::text('F20_from_no', $F20_form_sn, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
         
           
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 text-red" style="margin-top: 14px;">
                        <b>@lang('petro::lang.date_range'): <span class="from_date"></span> @lang('petro::lang.to') <span class="to_date"></span> </b>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}}  <br>
                                <span class="f20_location_name">@lang('petro::lang.all')</span></h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.f20_form') @lang('mpcs::lang.form_no') : {{$F20_form_sn}}</h5>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_20_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.index_no')</th>
                                    <th>@lang('mpcs::lang.product_code')</th>
                                    <th>@lang('mpcs::lang.product')</th>
                                    <th>@lang('mpcs::lang.sold_qty')</th>
                                    <th>@lang('mpcs::lang.unit_price')</th>
                                    <th>@lang('mpcs::lang.total_amount')</th>
        
                                </tr>
                            </thead>
                            <tfoot class="bg-gray">
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.cash_sale')</td>
                                    <td class="text-red text-bold" id="cash_sale"></td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.credit_sale')</td>
                                    <td class="text-red text-bold" id="credit_sale"></td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.grand_total')</td>
                                    <td class="text-red text-bold" id="grand_total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- <div class="row bg-gray" style="margin-top: 40px;">
                  
                </div> --}}
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->