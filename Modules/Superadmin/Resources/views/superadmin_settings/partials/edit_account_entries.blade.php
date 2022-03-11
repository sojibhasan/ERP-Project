<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        @can('account.access')
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="@if(!session('status.account_default')) active @endif">
                            <a href="#edit_account_entries" data-toggle="tab">
                                <i class="fa fa-book"></i>
                                <strong>@lang('superadmin::lang.edit_account_entries')</strong>
                            </a>
                        </li>
                        <li class="@if(session('status.account_default')) active @endif">
                            <a href="#list_edit_account_entries" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.list_edit_account_entries') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane @if(!session('status.account_default')) active @endif"
                            id="edit_account_entries">
                            <div class="row">
                                @component('components.filters', ['title' => __('report.filters')])
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('edit_account_date_range', __('report.date_range') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            {!! Form::text('edit_account_date_range', null, ['class' => 'form-control',
                                            'readonly', 'placeholder' => __('report.date_range')]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('business_id', __('account.business') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('business_id', $businesses, '', ['class' => 'form-control
                                            select2', 'style' => 'width: 100%;']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('account_id', __('account.account') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('account_id', [], null, ['class' => 'form-control select2',
                                            'style' => 'width: 100%;']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('transaction_type', ['' => __('messages.all'), 'deposit' =>
                                            'Deposit', 'fund_transfer' => 'Transfer'], '', ['class' => 'form-control'])
                                            !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('debit_credit', __('account.debit_credit') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('debit_credit', ['' => __('messages.all'),'debit' =>
                                            __('account.debit'),
                                            'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                                @endcomponent
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box">
                                            <div class="box-body">
                                                @can('account.access')
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="account_book"
                                                        style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang( 'messages.action' )</th>
                                                                <th>@lang( 'messages.date' )</th>
                                                                <th>@lang( 'lang_v1.description' )</th>
                                                                <th>@lang( 'lang_v1.note' ) </th>
                                                                <th>@lang( 'lang_v1.image' )</th>
                                                                <th>@lang( 'lang_v1.added_by' )</th>
                                                                <th>@lang('account.debit')</th>
                                                                <th>@lang('account.credit')</th>
                                                                <th>@lang( 'lang_v1.balance' )</th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr class="bg-gray font-17 text-center footer-total">

                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane @if(session('status.account_default')) active @endif"
                            id="list_edit_account_entries">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box">
                                        <div class="box-body">
                                            @can('account.access')
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped"
                                                    id="list_edit_account_book" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang( 'superadmin::lang.date_and_time' )</th>
                                                            <th>@lang( 'superadmin::lang.company_name' )</th>
                                                            <th>@lang( 'superadmin::lang.account_name' )</th>
                                                            <th>@lang( 'superadmin::lang.orignal_amount' )</th>
                                                            <th>@lang( 'superadmin::lang.edit_amount' )</th>
                                                            <th>@lang( 'superadmin::lang.action_type' )</th>
                                                        </tr>
                                                    </thead>

                                                </table>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="modal fade account_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"
            id="account_type_modal">
        </div>

    </section>

</div>