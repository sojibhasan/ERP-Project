<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>F22-{{$details['F22_from_no']}} {{request()->session()->get('business.name')}} {{$details['f22_location_name']}}</title>
    {{-- @include('layouts.partials.css') --}}
</head>
<style>

    table {
        border-collapse: collapse;
    }
    table tbody td {
        border: 1px solid black; 
    }
    table thead th {
        border: 1px solid black; 
    }
    th{
        font-size: 13px;
    }
    td{
        font-size: 13px;
    }
</style>
<body>
    <div class="col-md-12" style="text-align:center;">
        <div class="row">
            <div class="col-md-5">
                <div class="text-center">
                    <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                        <span class="f22_location_name">{{$details['f22_location_name']}}</span></h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center pull-left">
                    <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.f22_form')
                        @lang('mpcs::lang.form_no') : {{$details['F22_from_no']}}</h5>
                </div>
            </div>
        </div>
        @php
        $index = 1;
        $chunk_number = !empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25;
        $chuncks = array_chunk($data ,$chunk_number );
        $pre_page_total_purchase = 0;
        $pre_page_total_sale = 0;
        $grand_page_total_purchase = 0;
        $grand_page_total_sale = 0;
        @endphp
        @foreach ($chuncks as $key => $detail)
        <div class="row" style="margin-top: 20px; page-break-after: always;">
                <table class="table table-bordered table-striped" style="width: 100%;" id="form_22_table">
                    <thead>
                        <tr>
                            <th>@lang('mpcs::lang.index_no')</th>
                            <th>@lang('mpcs::lang.code')</th>
                            <th>@lang('mpcs::lang.book_no')</th>
                            <th>@lang('mpcs::lang.product')</th>
                            <th>@lang('mpcs::lang.current_stock')</th>
                            <th>@lang('mpcs::lang.stock_count')</th>
                            <th>@lang('mpcs::lang.unit_purchase_price')</th>
                            <th>@lang('mpcs::lang.total_purchase_price')</th>
                            <th>@lang('mpcs::lang.unit_sale_price')</th>
                            <th>@lang('mpcs::lang.total_sale_price')</th>
                            <th>@lang('mpcs::lang.qty_difference')</th>

                        </tr>
                    </thead>
                    @php
                    $this_page_total_purchase = 0.00;
                    $this_page_total_sale = 0.00;
                    @endphp
                    <tbody>
                        @foreach ($detail as $item)
                        <tr>
                            <td>{{$index}}</td>
                            <td>{{!empty($item['sku']) ? $item['sku'] : ''}}</td>
                            <td>{{!empty($item['book_no']) ? $item['book_no'] : ''}}</td>
                            <td>{{!empty($item['product']) ? $item['product'] : ''}}</td>
                            <td>{{!empty($item['current_stock']) ? @number_format($item['current_stock']) : ''}}</td>
                            <td>{{!empty($item['stock_count']) ? @number_format($item['stock_count']) : ''}}</td>
                            <td>{{!empty($item['unit_purchase_price']) ? @number_format($item['unit_purchase_price']) : ''}}</td>
                            <td>{{!empty($item['total_purhcase_value']) ? @number_format($item['total_purhcase_value']) : ''}}</td>
                            <td>{{!empty($item['unit_sale_price']) ? @number_format($item['unit_sale_price']) : ''}}</td>
                            <td>{{!empty($item['total_sale_value']) ? @number_format($item['total_sale_value']) : ''}}</td>
                            <td>{{!empty($item['difference_qty']) ? @number_format($item['difference_qty']) : ''}}</td>
                        </tr>

                        @php
                            $index++;

                            $this_page_total_purchase += !empty($item['total_purhcase_value']) ? $item['total_purhcase_value'] : 0;
                            $this_page_total_sale += !empty($item['total_sale_value']) ? $item['total_sale_value'] : 0;
                            $grand_page_total_purchase += !empty($item['total_purhcase_value']) ? $item['total_purhcase_value'] : 0;
                            $grand_page_total_sale += !empty($item['total_sale_value']) ? $item['total_sale_value'] : 0;
                            if($key == 0){
                                $pre_page_total_purchase = 0;
                                $pre_page_total_sale = 0;
                            }
                        @endphp
                        @endforeach

                    </tbody>
                    <tfoot class="bg-gray">
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_this_page')</td>
                            <td class="text-red text-bold" id="footer_total_purchase_price">
                                {{@number_format($this_page_total_purchase)}}</td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="2" id="footer_total_sale_price">
                                {{@number_format($this_page_total_sale)}}</td>
                        </tr>
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_previous_page')
                            </td>
                            <td class="text-red text-bold" id="pre_total_purchase_price">{{ @number_format($pre_page_total_purchase)}}
                            </td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="2" id="pre_total_sale_price">
                                {{ @number_format($pre_page_total_sale) }}</td>
                        </tr>
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.grand_total')</td>
                            <td class="text-red text-bold" id="grand_total_purchase_price">
                                {{@number_format($grand_page_total_purchase)}}</td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="2" id="grand_total_sale_price">
                                {{@number_format($grand_page_total_sale)}}</td>
                        </tr>
                        <tr>
                            <td colspan="11"> @lang('mpcs::lang.confirm_f22')</td>
                        </tr>
                        <tr>
                            <td colspan="7"  class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('mpcs::lang.checked_by'): ____________</h5>
                            </td>
                            <td colspan="4" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('mpcs::lang.received_by'): ____________</h5> <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('mpcs::lang.signature_of_manager'): ____________</h5>
                            </td>
                            <td colspan="4" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('mpcs::lang.handed_over_by'): ____________</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7"  class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-top: 10px; ">@lang('mpcs::lang.user'):
                                    {{auth()->user()->username }}</h5>
                            </td>
                            <td colspan="4" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                   </h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @php
                $pre_page_total_purchase = $grand_page_total_purchase;
                $pre_page_total_sale = $grand_page_total_sale;
            @endphp
            @endforeach
    </div>


    {{-- @include('layouts.partials.javascripts') --}}
 
</body>

</html>