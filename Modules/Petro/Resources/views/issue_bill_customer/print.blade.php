<div class="col-md-12">
    <div class="title text-center">
        <h3>{{request()->session()->get('business.name')}}</h3>
    </div>

    <div class="row">
        <div class="text-center">
            <h3>@lang('petro::lang.customer_bill')</h3>
        </div>
        <div class="col-md-6">
            <div class="col-md-6">
                <label>@lang('petro::lang.date')</label> {{$issue_customer_bill->date}}
            </div>
            <div class="col-md-6">
                <label>@lang('petro::lang.bill_no')</label> {{$issue_customer_bill->customer_bill_no}}
            </div>
            <div class="col-md-6">
                <label>@lang('petro::lang.order_no')</label> {{$issue_customer_bill->order_bill_no}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-6">
                <label>@lang('petro::lang.customer_name')</label> {{$issue_customer_bill->customer_name}}
            </div>
            <div class="col-md-6">
                <label>@lang('petro::lang.vehicle_no')</label> {{$issue_customer_bill->reference}}
            </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table-bordered table-striped table">
            <thead>
                <th>@lang('petro::lang.product')</th>
                <th>@lang('petro::lang.unit_price')</th>
                <th>@lang('petro::lang.qty')</th>
                <th>@lang('petro::lang.sub_total')</th>
            </thead>\

            <tbody>
                @foreach ($bill_details as $item)
                <tr>
                    <td>
                        {{$item->product_name}}
                    </td>
                    <td>
                        {{$item->unit_price}}
                    </td>
                    <td>
                        {{$item->qty}}
                    </td>
                    <td>
                        {{$item->sub_total}}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right">@lang('petro::lang.total_amount')</td>
                    <td>{{$issue_customer_bill->total_amount}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>