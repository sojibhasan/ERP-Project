
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_income_method')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Superadmin\Http\Controllers\IncomeMethodController@create')}}"
                data-container=".view_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="income_method_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.action')</th>
                    <th>@lang('superadmin::lang.date')</th>
                    <th>@lang('superadmin::lang.referral_group')</th>
                    <th>@lang('superadmin::lang.income_method')</th>
                    <th>@lang('superadmin::lang.income_method_status')</th>
                    <th>@lang('superadmin::lang.income_type')</th>
                    <th>@lang('superadmin::lang.value')</th>
                    <th>@lang('superadmin::lang.minimum_new_signups')</th>
                    <th>@lang('superadmin::lang.minimum_active_subscriptions')</th>
                    <th>@lang('superadmin::lang.comission_eligible_conditions')</th>
                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->
