<div class="pos-tab-content">
    @component('components.widget', ['class' => 'box-default', 'title' => __( 'superadmin::lang.default_business_categories' )])
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('BusinessCategoryController@create')}}"
            data-container=".edit_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="business_categories" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang( 'superadmin::lang.category_name' )</th>
                    <th>@lang( 'superadmin::lang.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
@endcomponent
</div>