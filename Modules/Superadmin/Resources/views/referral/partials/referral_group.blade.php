
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_referral_group')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Superadmin\Http\Controllers\ReferralGroupController@create')}}"
                data-container=".view_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="referral_group_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.date')</th>
                    <th>@lang('superadmin::lang.group_name')</th>
                    <th>@lang('superadmin::lang.added_by')</th>
                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->
