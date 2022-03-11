<div class="col-md-12">
    @component('components.widget', ['class' => 'box-primary'])
    <div class="col-md-5">
        <table class="table table-bordered table-striped" id="daily_report_table">
            <thead>
                <tr>
                    <th>@lang('report.product')</th>
                    <th>@lang('lang_v1.quantity')</th>
                    <th>@lang('lang_v1.value')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product_one)
                <tr>
                    <td>{{$product_one->name}}</td>
                    <td>{{@format_quantity($sell_one_arr[$product_one->id]['qty'])}}</td>
                    <td>{{@num_format($sell_one_arr[$product_one->id]['value'])}}</td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-5">
        <table class="table table-bordered table-striped" id="daily_report_table">
            <thead>
                <tr>
                    <th>@lang('report.product')</th>
                    <th>@lang('lang_v1.quantity')</th>
                    <th>@lang('lang_v1.value')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product_two)
                <tr>
                    <td>{{$product_two->name}}</td>
                    <td>{{@format_quantity($sell_two_arr[$product_two->id]['qty'])}}</td>
                    <td>{{@num_format($sell_two_arr[$product_two->id]['value'])}}</td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-2">
        <table class="table table-bordered table-striped" id="daily_report_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.quantity')</th>
                    <th>@lang('lang_v1.value')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product_two)
                <tr>
                    <td>{{@format_quantity($sell_one_arr[$product_two->id]['qty'] - $sell_two_arr[$product_two->id]['qty'])}}</td>
                    <td>{{@num_format($sell_one_arr[$product_two->id]['value'] - $sell_two_arr[$product_two->id]['value'])}}</td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>
    @endcomponent
</div>