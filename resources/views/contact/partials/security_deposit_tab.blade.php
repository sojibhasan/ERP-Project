<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="security_deposit_table" style="width: 100%">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('lang_v1.description')</th>
                    <!-- <th>@lang('lang_v1.amount')</th> -->
                    <th>@lang('account.credit')</th>
                    <th>@lang('account.debit')</th>
                    <th>@lang('lang_v1.user')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</div>