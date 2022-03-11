<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        @can('account.access')
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="@if(!session('status.account_default')) active @endif">
                            <a href="#edit_contact_entries" data-toggle="tab">
                                <i class="fa fa-book"></i>
                                <strong>@lang('superadmin::lang.edit_contact_entries')</strong>
                            </a>
                        </li>
                        <li class="@if(session('status.account_default')) active @endif">
                            <a href="#list_edit_contact_entries" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('superadmin::lang.list_edit_contact_entries') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane @if(!session('status.account_default')) active @endif"
                            id="edit_contact_entries">
                            <div class="row">
                                @component('components.filters', ['title' => __('report.filters')])
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('edit_contact_date_range', __('report.date_range') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            {!! Form::text('edit_contact_date_range', null, ['class' => 'form-control',
                                            'readonly', 'placeholder' => __('report.date_range')]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('contact_business_id', __('account.business') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('contact_business_id', $businesses, '', ['class' => 'form-control
                                            select2', 'style' => 'width: 100%;']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('contact_type', __('superadmin::lang.contact_type') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('contact_type', ['customer' =>__('superadmin::lang.customer'), 'supplier' => __('superadmin::lang.supplier')], null, ['class' => 'form-control select2',
                                            'style' => 'width: 100%;']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('contact_id', __('superadmin::lang.contact') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            {!! Form::select('contact_id', [], null, ['class' => 'form-control select2',
                                            'style' => 'width: 100%;']) !!}
                                        </div>
                                    </div>
                                </div>
                              
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('contact_debit_credit', __('account.debit_credit') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                                            {!! Form::select('contact_debit_credit', ['' => __('messages.all'),'debit' =>
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
                                                    <table class="table table-bordered table-striped" id="contact_ledger"
                                                        style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang( 'messages.action' )</th>
                                                                <th>@lang( 'superadmin::lang.date' )</th>
                                                                <th>@lang( 'superadmin::lang.reference_no' )</th>
                                                                <th>@lang( 'superadmin::lang.type' )</th>
                                                                <th>@lang( 'superadmin::lang.location' )</th>
                                                                <th>@lang( 'superadmin::lang.payment_status' )</th>
                                                                <th>@lang( 'superadmin::lang.debit' )</th>
                                                                <th>@lang( 'superadmin::lang.credit' )</th>
                                                                <th>@lang( 'superadmin::lang.balance' )</th>
                                                                <th>@lang( 'superadmin::lang.cheque_number' )</th>
                                                                <th>@lang( 'superadmin::lang.payment_method' )</th>
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
                            id="list_edit_contact_entries">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box">
                                        <div class="box-body">
                                            @can('account.access')
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped"
                                                    id="list_edit_contact_ledger" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang( 'superadmin::lang.date_and_time' )</th>
                                                            <th>@lang( 'superadmin::lang.company_name' )</th>
                                                            <th>@lang( 'superadmin::lang.contact_name' )</th>
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