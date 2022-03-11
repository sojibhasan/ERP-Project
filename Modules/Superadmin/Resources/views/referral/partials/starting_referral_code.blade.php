
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_referral_starting_code')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Superadmin\Http\Controllers\ReferralStartingCodeController@create')}}"
                data-container=".view_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="referral_starting_code_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.date')</th>
                    <th>@lang('superadmin::lang.referral_group')</th>
                    <th>@lang('superadmin::lang.prefix')</th>
                    <th>@lang('superadmin::lang.starting_code')</th>
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
