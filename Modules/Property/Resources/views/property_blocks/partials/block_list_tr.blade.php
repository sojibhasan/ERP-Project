<table class="table table-bordered table-striped" id="block_list_table">
    <thead>
        <tr>
            <th>@lang('property::lang.block_out_date')</th>
            <th>@lang('property::lang.block_number')</th>
            <th>@lang('property::lang.block_extent')</th>
            <th>@lang('property::lang.units')</th>
            <th>@lang('property::lang.per_unit_price')</th>
            <th>@lang('property::lang.block_sale_price')</th>
            <th>@lang('property::lang.block_sold_price')</th>
            <th width="10%">@lang('property::lang.customer')</th>
            <th>@lang('property::lang.sale_officers')</th>
            <th>NIC Number</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($property_blocks as $block)


        <tr>
            <td>{{$block->transaction_date}}</td>
            <td>{{$block->block_number}}</td>
            <td>{{@format_quantity($block->block_extent)}}</td>
            <td>{{$block->actual_name}}</td>
            <td>@if(!empty($block->block_sold_price) && $block->block_sold_price >0)
                {{@num_format($block->block_sold_price/$block->block_extent)}} @else
                {{@num_format($block->block_sale_price/$block->block_extent)}} @endif</td>
            <td>{{@num_format($block->block_sale_price)}}</td>
            <td>{{@num_format($block->block_sold_price)}}</td>
            <td>
                @if(!empty($block->customer_id))
                <a href="#"
                    data-href="{{action('\Modules\Property\Http\Controllers\PropertyController@getCustomerDetails', [$block->customer_id])}}"
                    class="btn-modal" data-container=".view_modal">{{$block->customer_name}}</a>
                @endif
            </td>
            <td>
                @if(!empty($block->sold_by))
                    {{ \App\User::find($block->sold_by)->username }}
                @endif
            </td>
            <td>{{$block->customer_nic_number}}</td>
        </tr>

        @endforeach
    </tbody>
    <tfoot>
        <tr class="text-red">
            <td colspan="6" class="text-right">@lang('property::lang.total')</td>
            <td>{{@num_format($property_blocks->sum('block_sold_price'))}}</td>

        </tr>
    </tfoot>
</table>