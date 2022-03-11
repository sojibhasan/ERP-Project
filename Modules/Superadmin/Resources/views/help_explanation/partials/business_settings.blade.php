<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('superadmin::lang.business_settings')])
    <div class="col-md-12">
        <div class="pos-tab-content">
            <div class="row">
                <!--  <pos-tab-container> -->
                <div class="col-xs-12 pos-tab-container">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                        <div class="list-group">
                            <a href="#" class="list-group-item text-center active">@lang('business.business')</a>
                            <a href="#" class="list-group-item text-center">@lang('business.sale')</a>
                            <a href="#" class="list-group-item text-center">@lang('sale.pos_sale')</a>
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.purchase')</a>
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.reward_point_settings')</a>
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.modules')</a>
                            <a href="#" class="list-group-item text-center">@lang('superadmin::lang.stores')</a>
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.restaurant')</a>
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.upload_images')</a>
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                        <!-- tab 1 start -->
                        <div class="pos-tab-content active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[default_profit_percent]', __('business.default_profit_percent') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[default_profit_percent]', !empty($help_explanations['default_profit_percent']) ? $help_explanations['default_profit_percent'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[financial_year_start_month]', __('superadmin::lang.financial_year_start_month') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[financial_year_start_month]', !empty($help_explanations['financial_year_start_month']) ? $help_explanations['financial_year_start_month'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[stock_accounting_method]', __('superadmin::lang.stock_accounting_method') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[stock_accounting_method]', !empty($help_explanations['stock_accounting_method']) ? $help_explanations['stock_accounting_method'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[transaction_edit_days]', __('superadmin::lang.transaction_edit_days') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[transaction_edit_days]', !empty($help_explanations['transaction_edit_days']) ? $help_explanations['transaction_edit_days'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[popup_load_auto_save_data]', __('superadmin::lang.popup_load_auto_save_data') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[popup_load_auto_save_data]', !empty($help_explanations['popup_load_auto_save_data']) ? $help_explanations['popup_load_auto_save_data'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[day_end]', __('superadmin::lang.day_end') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[day_end]', !empty($help_explanations['day_end']) ? $help_explanations['day_end'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_line_discount]', __('superadmin::lang.enable_line_discount') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_line_discount]', !empty($help_explanations['enable_line_discount']) ? $help_explanations['enable_line_discount'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[need_to_show_for_the_customer]', __('superadmin::lang.need_to_show_for_the_customer') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[need_to_show_for_the_customer]', !empty($help_explanations['need_to_show_for_the_customer']) ? $help_explanations['need_to_show_for_the_customer'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- tab 1 end -->
                        <!-- tab 2 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[sale_price_is_minimum_selling_price]', __('superadmin::lang.sale_price_is_minimum_selling_price') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[sale_price_is_minimum_selling_price]', !empty($help_explanations['sale_price_is_minimum_selling_price']) ? $help_explanations['sale_price_is_minimum_selling_price'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[allow_overselling]', __('superadmin::lang.allow_overselling') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[allow_overselling]', !empty($help_explanations['allow_overselling']) ? $help_explanations['allow_overselling'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[sold_product_list_in_the_register_report]', __('superadmin::lang.sold_product_list_in_the_register_report') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[sold_product_list_in_the_register_report]', !empty($help_explanations['sold_product_list_in_the_register_report']) ? $help_explanations['sold_product_list_in_the_register_report'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_line_discount]', __('superadmin::lang.enable_line_discount') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_line_discount]', !empty($help_explanations['enable_line_discount']) ? $help_explanations['enable_line_discount'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[below_cost_price]', __('superadmin::lang.below_cost_price') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[below_cost_price]', !empty($help_explanations['below_cost_price']) ? $help_explanations['below_cost_price'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                       
                        <!-- tab 2 end -->
                        <!-- tab 3 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[subtotal_editable]', __('superadmin::lang.subtotal_editable') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[subtotal_editable]', !empty($help_explanations['subtotal_editable']) ? $help_explanations['subtotal_editable'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_service_staff_in_product_line]', __('superadmin::lang.enable_service_staff_in_product_line') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_service_staff_in_product_line]', !empty($help_explanations['enable_service_staff_in_product_line']) ? $help_explanations['enable_service_staff_in_product_line'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[show_credit_sale_button]', __('superadmin::lang.show_credit_sale_button') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[show_credit_sale_button]', !empty($help_explanations['show_credit_sale_button']) ? $help_explanations['show_credit_sale_button'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[disable_duplicate_invoice]', __('superadmin::lang.disable_duplicate_invoice') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[disable_duplicate_invoice]', !empty($help_explanations['disable_duplicate_invoice']) ? $help_explanations['disable_duplicate_invoice'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <!-- tab 3 end -->
                        <!-- tab 4 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_editing_product_price_from_purchase_screen]', __('superadmin::lang.enable_editing_product_price_from_purchase_screen') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_editing_product_price_from_purchase_screen]', !empty($help_explanations['enable_editing_product_price_from_purchase_screen']) ? $help_explanations['enable_editing_product_price_from_purchase_screen'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_purchase_status]', __('superadmin::lang.enable_purchase_status') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_purchase_status]', !empty($help_explanations['enable_purchase_status']) ? $help_explanations['enable_purchase_status'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_lot_number]', __('superadmin::lang.enable_lot_number') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_lot_number]', !empty($help_explanations['enable_lot_number']) ? $help_explanations['enable_lot_number'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[enable_free_qty]', __('superadmin::lang.enable_free_qty') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[enable_free_qty]', !empty($help_explanations['enable_free_qty']) ? $help_explanations['enable_free_qty'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 4 end -->
                        <!-- tab 5 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[amount_spend_for_unit_point]', __('superadmin::lang.amount_spend_for_unit_point') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[amount_spend_for_unit_point]', !empty($help_explanations['amount_spend_for_unit_point']) ? $help_explanations['amount_spend_for_unit_point'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[minimum_order_total_to_earn_reward]', __('superadmin::lang.minimum_order_total_to_earn_reward') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[minimum_order_total_to_earn_reward]', !empty($help_explanations['minimum_order_total_to_earn_reward']) ? $help_explanations['minimum_order_total_to_earn_reward'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[maximum_point_per_order]', __('superadmin::lang.maximum_point_per_order') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[maximum_point_per_order]', !empty($help_explanations['maximum_point_per_order']) ? $help_explanations['maximum_point_per_order'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[redeem_amount_per_unit_point]', __('superadmin::lang.redeem_amount_per_unit_point') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[redeem_amount_per_unit_point]', !empty($help_explanations['redeem_amount_per_unit_point']) ? $help_explanations['redeem_amount_per_unit_point'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[minimum_order_total_to_redeem_points]', __('superadmin::lang.minimum_order_total_to_redeem_points') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[minimum_order_total_to_redeem_points]', !empty($help_explanations['minimum_order_total_to_redeem_points']) ? $help_explanations['minimum_order_total_to_redeem_points'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[minimum_redeem_point]', __('superadmin::lang.minimum_redeem_point') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[minimum_redeem_point]', !empty($help_explanations['minimum_redeem_point']) ? $help_explanations['minimum_redeem_point'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[maximum_redeem_point_per_order]', __('superadmin::lang.maximum_redeem_point_per_order') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[maximum_redeem_point_per_order]', !empty($help_explanations['maximum_redeem_point_per_order']) ? $help_explanations['maximum_redeem_point_per_order'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[reward_point_expiry_period]', __('superadmin::lang.reward_point_expiry_period') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[reward_point_expiry_period]', !empty($help_explanations['reward_point_expiry_period']) ? $help_explanations['reward_point_expiry_period'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 5 end -->
                        <!-- tab 6 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[tables]', __('superadmin::lang.tables') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[tables]', !empty($help_explanations['tables']) ? $help_explanations['tables'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[modifiers]', __('superadmin::lang.modifiers') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[modifiers]', !empty($help_explanations['modifiers']) ? $help_explanations['modifiers'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[subscription]', __('superadmin::lang.enable_subcription') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[subscription]', !empty($help_explanations['subscription']) ? $help_explanations['subscription'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[type_of_service]', __('superadmin::lang.types_of_service') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[type_of_service]', !empty($help_explanations['type_of_service']) ? $help_explanations['type_of_service'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 6 end -->
                        <!-- tab 7 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[default_store]', __('superadmin::lang.default_store') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[default_store]', !empty($help_explanations['default_store']) ? $help_explanations['default_store'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 7 end -->
                        <!-- tab 8 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[res_tables]', __('superadmin::lang.tables') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[res_tables]', !empty($help_explanations['res_tables']) ? $help_explanations['res_tables'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[res_modifiers]', __('superadmin::lang.modifiers') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[res_modifiers]', !empty($help_explanations['res_modifiers']) ? $help_explanations['res_modifiers'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[service_staff]', __('superadmin::lang.service_staff') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[res_service_staff]', !empty($help_explanations['res_service_staff']) ? $help_explanations['res_service_staff'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[kitchen_for_restaurant]', __('superadmin::lang.kitchen_for_restaurant') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[res_kitchen]', !empty($help_explanations['res_kitchen']) ? $help_explanations['res_kitchen'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 8 end -->
                        <!-- tab 9 start -->
                        <div class="pos-tab-content ">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[login_page_showing_type]', __('superadmin::lang.login_page_showing_type') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[login_page_showing_type]', !empty($help_explanations['login_page_showing_type']) ? $help_explanations['login_page_showing_type'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('help_explanation[backgroud_image]', __('superadmin::lang.backgroud_image') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('help_explanation[backgroud_image]', !empty($help_explanations['backgroud_image']) ? $help_explanations['backgroud_image'] : null, ['class' => 'form-control']); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab 9 end -->

                    </div>
                </div>
                <!--  </pos-tab-container> -->
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->