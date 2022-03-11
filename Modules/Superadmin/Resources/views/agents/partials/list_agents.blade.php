
<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_agents')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="agents_table">
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.date')</th>
                    <th>@lang('superadmin::lang.referral_code')</th>
                    <th>@lang('superadmin::lang.name')</th>
                    <th>@lang('superadmin::lang.mobile_number')</th>
                    <th>@lang('superadmin::lang.email')</th>
                    <th>@lang('superadmin::lang.referral_group')</th>
                    <th>@lang('superadmin::lang.total_orders')</th>
                    <th>@lang('superadmin::lang.active_subscription')</th>
                    <th>@lang('superadmin::lang.income')</th>
                    <th>@lang('superadmin::lang.paid')</th>
                    <th>@lang('superadmin::lang.due')</th>
                    <th>@lang('superadmin::lang.action')</th>

                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>

    @endcomponent
</section>
<!-- /.content -->