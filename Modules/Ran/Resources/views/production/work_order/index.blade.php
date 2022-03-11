<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('ran::lang.all_your_work_order')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Ran\Http\Controllers\WorkOrderController@create')}}"
                data-container=".production_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="work_order_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('ran::lang.date_and_time')</th>
                    <th>@lang('ran::lang.business_location')</th>
                    <th>@lang('ran::lang.work_order_no')</th>
                    <th>@lang('ran::lang.customer_order_no')</th>
                    <th>@lang('ran::lang.goldsmith')</th>
                    <th>@lang('ran::lang.received_work_order_no')</th>
                    <th>@lang('ran::lang.order_delivery_date')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->