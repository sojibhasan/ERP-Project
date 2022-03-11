@component('components.widget', ['class' => '', 'title' => __( 'lang_v1.all_your_customer_groups' )])
@can('customer.create')
@slot('tool')
<div class="box-tools">
    <button type="button" class="btn btn-block btn-primary btn-modal" id="add_group_btn"
        data-href="{{action('ContactGroupController@create')}}?type=customer" data-container=".contact_groups_modal">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
</div>
@endslot
@endcan
@can('customer.view')
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="customer_groups_table">
        <thead>
            <tr>
                <th>@lang( 'lang_v1.customer_name' )</th>
                <th>@lang( 'lang_v1.calculation_percentage' )</th>
                <th>@lang( 'lang_v1.account_type' )</th>
                <th>@lang( 'lang_v1.interest_income_account' )</th>
                <th>@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>
@endcan
@endcomponent