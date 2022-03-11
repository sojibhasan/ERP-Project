<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-md-12">

            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">

                <div class="form-group">

                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}

                    {!! Form::select('report_location_id', $business_locations, null, ['class' => 'form-control

                    select2',

                    'placeholder' => __('petro::lang.all'), 'id' => 'report_location_id', 'style' => 'width:100%']); !!}

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}

                    {!! Form::select('report_tank_id', $tanks, null, ['class' => 'form-control select2', 'placeholder'

                    => __('petro::lang.all'), 'id' => 'report_tank_id', 'style' => 'width:100%']); !!}

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    {!! Form::label('products', __('petro::lang.products') . ':') !!}

                    {!! Form::select('report_product_id', $products, null, ['class' => 'form-control select2',

                    'placeholder'

                    => __('petro::lang.all'), 'id' => 'report_product_id', 'style' => 'width:100%']); !!}

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    {!! Form::label('daily_report_date_range', __('report.date_range') . ':') !!}

                    {!! Form::text('report_date_range', @format_date('first day of this month') . ' ~ ' .

                    @format_date('last

                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>

                    'form-control', 'id' => 'report_date_range', 'readonly']); !!}

                </div>

            </div>

            @endcomponent

        </div>

    </div>



    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.dip_report')])

    @slot('tool')

    <button type="button" class="btn  btn-primary btn-modal pull-right"

    data-href="{{action('\Modules\Petro\Http\Controllers\DipManagementController@addNewDip')}}"

    data-container=".dip_modal">

    <i class="fa fa-thermometer"></i> @lang('petro::lang.add_dip')</button>

    

    @endslot

    <div class="col-md-12">

        <div class="row">

            <div class="col-md-5 text-red" style="margin-top: 14px;">

                <b>@lang('petro::lang.date_range'): <span class="report_from_date"></span> @lang('petro::lang.to') <span

                        class="report_to_date"></span> </b>

            </div>

            <div class="col-md-7">

                <div class="text-center pull-left">

                    <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>

                        <span class="report_location_name">@lang('petro::lang.all')</span></h5>

                </div>

            </div>

        </div>

        <div class="row" style="margin-top: 15px;">

          

            {{-- <div class="col-md-3"><b>@lang('petro::lang.tank'): <span class="report_tank"></span></b></div>

            <div class="col-md-3"><b>@lang('petro::lang.product'): <span class="report_product"></span></b></div>

            <div class="col-md-2"><b>@lang('petro::lang.total_loss'): <span class="report_total_loss"></span></b></div>

            <div class="col-md-2"><b>@lang('petro::lang.total_excess'): <span class="report_total_excess"></span></b></div>

            <div class="col-md-2"><b>@lang('petro::lang.net_difference'): <span class="report_net_difference"></span></b></div> --}}

        </div>

        <div class="row" style="margin-top: 20px;">

            <div class="table-responsive">

                <table class="table table-bordered table-striped" id="dip_report_table">

                    <thead>

                        <tr>

                            <th>@lang('petro::lang.add_dip_no')</th>

                            <th>@lang('petro::lang.date')</th>

                            <th>@lang('petro::lang.location')</th>

                            <th>@lang('petro::lang.tank')</th>

                            <th>@lang('petro::lang.product')</th>

                            <th>@lang('petro::lang.dip_reading')</th>

                            <th>@lang('petro::lang.qty_on_dip_reading')</th>

                            <th>@lang('petro::lang.current_qty')</th>

                            <th>@lang('petro::lang.differnece')</th>
                            
                            <th>@lang('petro::lang.difference_value')</th>

                        </tr>

                    </thead>

                    
<tfoot>                        

                        <tr class="footer_total">

                            <td colspan="8" style="text-align: right; font-weight: bold;">@lang('petro::lang.total')

                                :</td>

                            <td style="text-align: left; font-weight: bold;" class="difference_total display_currency"></td>

                            <td style="text-align: left; font-weight: bold;" class="difference_value_total display_currency final-total"></td>

                        </tr>                        

                    </tfoot>
                   

                </table>

            </div>

        </div>

    </div>

    @endcomponent



    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>

<!-- /.content -->