<div class="pos-tab-content  @if($get_permissions['property_module'] == 1) hide  @endif">
    <h4>@lang('business.add_keyboard_shortcuts'):</h4>
    <p class="help-block">@lang('lang_v1.shortcut_help'); @lang('lang_v1.example'): <b>ctrl+shift+b</b>, <b>ctrl+h</b>
    </p>
    <p class="help-block">
        <b>@lang('lang_v1.available_key_names_are'):</b>
        <br> shift, ctrl, alt, backspace, tab, enter, return, capslock, esc, escape, space, pageup, pagedown, end, home,
        <br>left, up, right, down, ins, del, and plus
    </p>
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-striped">
                <tr>
                    <th>@lang('business.operations')</th>
                    <th>@lang('business.keyboard_shortcut')</th>
                </tr>
                <tr>
                    <td>{!! __('sale.express_finalize') !!}:</td>
                    <td>
                        {!! Form::text('shortcuts[pos][express_checkout]',
                        !empty($shortcuts["pos"]["express_checkout"]) ? $shortcuts["pos"]["express_checkout"] : null,
                        ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.finalize'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][pay_n_ckeckout]', !empty($shortcuts["pos"]["pay_n_ckeckout"]) ?
                        $shortcuts["pos"]["pay_n_ckeckout"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.draft'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][draft]', !empty($shortcuts["pos"]["draft"]) ?
                        $shortcuts["pos"]["draft"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('messages.cancel'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][cancel]', !empty($shortcuts["pos"]["cancel"]) ?
                        $shortcuts["pos"]["cancel"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('lang_v1.recent_product_quantity'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][recent_product_quantity]',
                        !empty($shortcuts["pos"]["recent_product_quantity"]) ?
                        $shortcuts["pos"]["recent_product_quantity"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                @if($enable_duplicate_invoice)
                <tr>
                    <td>@lang('lang_v1.duplicate_invoice'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][duplicate_invoice]',
                        !empty($shortcuts["pos"]["duplicate_invoice"]) ? $shortcuts["pos"]["duplicate_invoice"] : null,
                        ['class' => 'form-control']); !!}
                    </td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table table-striped">
                <tr>
                    <th>@lang('business.operations')</th>
                    <th>@lang('business.keyboard_shortcut')</th>
                </tr>
                <tr>
                    <td>@lang('sale.edit_discount'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][edit_discount]', !empty($shortcuts["pos"]["edit_discount"]) ?
                        $shortcuts["pos"]["edit_discount"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.edit_order_tax'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][edit_order_tax]', !empty($shortcuts["pos"]["edit_order_tax"]) ?
                        $shortcuts["pos"]["edit_order_tax"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.add_payment_row'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][add_payment_row]', !empty($shortcuts["pos"]["add_payment_row"]) ?
                        $shortcuts["pos"]["add_payment_row"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.finalize_payment'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][finalize_payment]', !empty($shortcuts["pos"]["finalize_payment"])
                        ? $shortcuts["pos"]["finalize_payment"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('lang_v1.add_new_product'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][add_new_product]', !empty($shortcuts["pos"]["add_new_product"]) ?
                        $shortcuts["pos"]["add_new_product"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @if($enable_duplicate_invoice)
    <div class="row">
        <div class="col-sm-12">
            <h4>@lang('lang_v1.duplicate_invoice_settings'):</h4>
        </div>
        <div class="">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('duplicate_invoice_prefix', __('lang_v1.duplicate_invoice_prefix')) !!}
                    {!! Form::text('pos_settings[duplicate_invoice_prefix]',
                    !empty($pos_settings['duplicate_invoice_prefix']) ?
                    $pos_settings['duplicate_invoice_prefix'] : null, ['class' => 'form-control', 'placeholder' =>
                    __('lang_v1.duplicate_invoice_prefix')]); !!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                        <br>
                        <label>
                            {!! Form::checkbox('pos_settings[enable_prefix_duplicate_invoice]', 1,
                            empty($pos_settings['enable_prefix_duplicate_invoice']) ? 0 : 1 ,
                            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_prefix_duplicate_invoice' ) }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <h4>@lang('lang_v1.pos_settings'):</h4>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_pay_checkout]', 1,
                        $pos_settings['disable_pay_checkout'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_pay_checkout' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_draft]', 1,
                        $pos_settings['disable_draft'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_draft' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_express_checkout]', 1,
                        $pos_settings['disable_express_checkout'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_express_checkout' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[hide_product_suggestion]', 1,
                        $pos_settings['hide_product_suggestion'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_product_suggestion' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[hide_recent_trans]', 1, $pos_settings['hide_recent_trans'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_recent_trans' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_discount]', 1, $pos_settings['disable_discount'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_discount' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_order_tax]', 1, $pos_settings['disable_order_tax'] ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_order_tax' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[is_pos_subtotal_editable]', 1,
                        empty($pos_settings['is_pos_subtotal_editable']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.subtotal_editable' ) }}
                    </label>
                    @if(!empty($help_explanations['subtotal_editable'])) @show_tooltip($help_explanations['subtotal_editable']) @endif
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_suspend]', 1,
                        empty($pos_settings['disable_suspend']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_suspend_sale' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[enable_transaction_date]', 1,
                        empty($pos_settings['enable_transaction_date']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_pos_transaction_date' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[inline_service_staff]', 1,
                        !empty($pos_settings['inline_service_staff']) ? true : false ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_service_staff_in_product_line' ) }}
                    </label>
                    @if(!empty($help_explanations['enable_service_staff_in_product_line'])) @show_tooltip($help_explanations['enable_service_staff_in_product_line']) @endif
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[is_service_staff_required]', 1,
                        empty($pos_settings['is_service_staff_required']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.is_service_staff_required' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[show_credit_sale_button]', 1,
                        empty($pos_settings['show_credit_sale_button']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_credit_sale_button' ) }}
                    </label>
                    @if(!empty($help_explanations['show_credit_sale_button'])) @show_tooltip($help_explanations['show_credit_sale_button']) @endif
                </div>
            </div>
        </div>
        @if($enable_duplicate_invoice)
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[disable_duplicate_invoice]', 1,
                        empty($pos_settings['disable_duplicate_invoice']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_duplicate_invoice' ) }}
                    </label>
                    @if(!empty($help_explanations['disable_duplicate_invoice'])) @show_tooltip($help_explanations['disable_duplicate_invoice']) @endif
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <br>
                    <label>
                        {!! Form::checkbox('pos_settings[price_later]', 1,
                        empty($pos_settings['price_later']) ? 0 : 1 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.price_later' ) }}
                    </label>
                    @if(!empty($help_explanations['price_later'])) @show_tooltip($help_explanations['price_later']) @endif
                </div>
            </div>
        </div>
    </div>
</div>