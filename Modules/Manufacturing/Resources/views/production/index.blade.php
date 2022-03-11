
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="productions_table" style="width: 100%;">
                 <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('purchase.location')</th>
                        <th>@lang('sale.product')</th>
                        <th>@lang('lang_v1.quantity')</th>
                        <th>@lang('manufacturing::lang.total_cost')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

