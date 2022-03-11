<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('ran::lang.all_your_list_productions')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Ran\Http\Controllers\ProductionController@create')}}"
                data-container=".production_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_production_table">
            <thead>
                <tr>
                    <th>@lang('ran::lang.date')</th>
                    <th>@lang('ran::lang.reference')</th>
                    <th>@lang('ran::lang.goldsmith')</th>
                    <th>@lang('ran::lang.qty')</th>
                    {{-- <th>@lang('ran::lang.warehouse')</th> --}}
                    <th>@lang('ran::lang.business_location')</th>
                    <th>@lang('ran::lang.total_product')</th>
                    <th>@lang('ran::lang.other_weight')</th>
                    <th>@lang('ran::lang.auto_calculation')</th>
                    <th>@lang('ran::lang.total_gold_waste')</th>
                    <th>@lang('ran::lang.goldsmith_wastage')</th>
                    <th>@lang('ran::lang.other_cost')</th>
                    {{-- <th class="notexport">@lang('messages.action')</th> --}}

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->