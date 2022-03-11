@component('components.widget', ['class' => '', 'title' => __( 'lang_v1.all_your_supplier_groups' )])
@can('customer.create')
@slot('tool')
<div class="box-tools">
    <button type="button" class="btn btn-block btn-primary btn-modal" id="add_group_btn"
        data-href="{{action('ContactGroupController@create')}}?type=supplier" data-container=".contact_groups_modal">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
</div>
@endslot
@endcan
@can('customer.view')
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="supplier_groups_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang( 'lang_v1.supplier_name' )</th>
                <th>@lang( 'lang_v1.calculation_percentage' )</th>
                <th>@lang( 'lang_v1.account_type' )</th>
                <th>@lang( 'lang_v1.interest_expense_account' )</th>
                <th>@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>
@endcan
@endcomponent