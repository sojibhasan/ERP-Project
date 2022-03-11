<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('ran::lang.all_your_list_wastage')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Ran\Http\Controllers\WastageController@create')}}"
                data-container=".goldsmith_model">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="wastage_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('ran::lang.date_and_time')</th>
                    <th>@lang('ran::lang.wastage_form_no')</th>
                    <th>@lang('ran::lang.business_location')</th>
                    <th>@lang('ran::lang.goldsmith')</th>
                    <th>@lang('ran::lang.sub_category')</th>
                    <th>@lang('ran::lang.wastage_per_8_g')</th>
                    <th>@lang('ran::lang.user')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->