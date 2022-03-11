<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.customer')}} & {{ __('report.supplier')}} {{ __('report.reports')}}</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('cg_customer_group_id', __( 'lang_v1.customer_group_name' ) . ':') !!}
                        {!! Form::select('cnt_customer_group_id', $customer_group, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'cnt_customer_group_id']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('type', __( 'lang_v1.type' ) . ':') !!}
                        {!! Form::select('contact_type', $types, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'contact_type']); !!}
                    </div>
                </div>
                <div class="col-md-3 customers hide">
                    <div class="form-group">
                        {!! Form::label('customer_id', __( 'lang_v1.customers' ) . ':') !!}
                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'customer_id']); !!}
                    </div>
                </div>
                <div class="col-md-3 suppliers hide">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __( 'lang_v1.suppliers' ) . ':') !!}
                        {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'supplier_id']); !!}
                    </div>
                </div>
                <div class="col-md-3 both hide">
                    <div class="form-group">
                        {!! Form::label('contact_id', __( 'lang_v1.contacts' ) . ':') !!}
                        {!! Form::select('contact_id', $both, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'both']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('contact_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('contact_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'contact_date_range', 'readonly']); !!}
                    </div>
                </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="supplier_report_tbl" style="width: 100%;">  
                    <thead>
                        <tr>
                            <th>@lang('report.contact')</th>
                            <th>@lang('report.total_purchase')</th>
                            <th>@lang('lang_v1.total_purchase_return')</th>
                            <th>@lang('report.supplier_opening_balance')</th> 
                            <th>@lang('report.total_sell')</th>
                            <th>@lang('lang_v1.total_sell_return')</th>
                            <th>@lang('report.customer_opening_balance')</th>
                            <th>@lang('report.due')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td><span class="display_currency" id="footer_total_purchase" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_purchase_return" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_supplier_opening_balance" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_sell" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_sell_return" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_customer_opening_balance" data-currency_symbol ="true"></span></td>
                            <td><span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

