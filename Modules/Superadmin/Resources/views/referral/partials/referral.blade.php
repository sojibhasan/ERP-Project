
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
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('referral_code', __('superadmin::lang.referral_code') . ':') !!}
            {!! Form::select('referral_code', $referral_codes, null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('package_id', __('superadmin::lang.packages') . ':') !!}
            {!! Form::select('package_id', $packages, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_referrals')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="referral_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.date')</th>
                    <th>@lang('superadmin::lang.referral_code')</th>
                    <th>@lang('superadmin::lang.name_of_new_registrantion')</th>
                    <th>@lang('superadmin::lang.code_of_new_registration')</th>
                    <th>@lang('superadmin::lang.package_selected')</th>

                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>

    @endcomponent
</section>
<!-- /.content -->
