<style>
    input[type="checkbox"][readonly] {
        pointer-events: none;
    }
</style>
<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#customer" data-toggle="tab">
                            <i class="fa fa-address-book"></i> <strong>@lang('lang_v1.customer')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#supplier" data-toggle="tab">
                            <i class="fa fa-address-book"></i> <strong>
                                @lang('lang_v1.supplier') </strong>
                        </a>
                    </li>
                    <li class="@if(!$get_permissions['property_module']) hide @endif">
                        <a href="#property_customer" data-toggle="tab">
                            <i class="fa fa-address-book"></i> <strong>
                                @lang('lang_v1.property_customer') </strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="customer">
                        <h3>@lang('lang_v1.select_the_field_you_want_in_adding_contact')</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_type]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.type')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_name]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.name')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_contact_id]', 1,
                                            1, ['class'
                                            => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.contact_id')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_tax_number]', 1,
                                            array_key_exists('customer_tax_number', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.tax_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_opening_balance]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.opening_balance')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_pay_term]', 1,
                                            array_key_exists('customer_pay_term', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.pay_term')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_transaction_date]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.transaction_date')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_customer_group]', 1,
                                            array_key_exists('customer_customer_group', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.customer_group')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_credit_limit]', 1,
                                            array_key_exists('customer_credit_limit', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.credit_limit')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_password]', 1,
                                            array_key_exists('customer_password', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.password')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_confirm_password]', 1,
                                            array_key_exists('customer_confirm_password', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.confirm_password')}}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_email]', 1,
                                            array_key_exists('customer_email', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.email')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_mobile]', 1,
                                            array_key_exists('customer_mobile', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.mobile')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_alternate_contact_number]', 1,
                                            array_key_exists('customer_alternate_contact_number',
                                            $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.alternate_contact_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_landline]', 1,
                                            array_key_exists('customer_landline', $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.landline')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_address]', 1,
                                            array_key_exists('customer_address', $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.address')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_city]', 1,
                                            array_key_exists('customer_city', $business->contact_fields ?? []), ['class'
                                            =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.city')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_state]', 1,
                                            array_key_exists('customer_state', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.state')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_country]', 1,
                                            array_key_exists('customer_country', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.country')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_landmark]', 1,
                                            array_key_exists('customer_landmark', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.landmark')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_custom_field_1]', 1,
                                            array_key_exists('customer_custom_field_1', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_1')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_custom_field_2]', 1,
                                            array_key_exists('customer_custom_field_2', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_2')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_custom_field_3]', 1,
                                            array_key_exists('customer_custom_field_3', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_3')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[customer_custom_field_4]', 1,
                                            array_key_exists('customer_custom_field_4', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_4')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="supplier">
                        <h3>@lang('lang_v1.select_the_field_you_want_in_adding_contact')</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_type]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.type')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_name]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.name')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_contact_id]', 1,
                                            1, ['class'
                                            => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.contact_id')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_tax_number]', 1,
                                            array_key_exists('supplier_tax_number', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.tax_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_opening_balance]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.opening_balance')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_pay_term]', 1,
                                            array_key_exists('supplier_pay_term', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.pay_term')}}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_transaction_date]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.transaction_date')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_supplier_group]', 1,
                                            array_key_exists('supplier_supplier_group', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.supplier_group')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_email]', 1,
                                            array_key_exists('supplier_email', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.email')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_mobile]', 1,
                                            array_key_exists('supplier_mobile', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.mobile')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_alternate_contact_number]', 1,
                                            array_key_exists('supplier_alternate_contact_number',
                                            $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.alternate_contact_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_landline]', 1,
                                            array_key_exists('supplier_landline', $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.landline')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_address]', 1,
                                            array_key_exists('supplier_address', $business->contact_fields ?? []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.address')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_city]', 1,
                                            array_key_exists('supplier_city', $business->contact_fields ?? []), ['class'
                                            =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.city')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_state]', 1,
                                            array_key_exists('supplier_state', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.state')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_country]', 1,
                                            array_key_exists('supplier_country', $business->contact_fields ?? []),
                                            ['class' =>
                                            'input-icheck']); !!}
                                            {{__('lang_v1.country')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_landmark]', 1,
                                            array_key_exists('supplier_landmark', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.landmark')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_custom_field_1]', 1,
                                            array_key_exists('supplier_custom_field_1', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_1')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_custom_field_2]', 1,
                                            array_key_exists('supplier_custom_field_2', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_2')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_custom_field_3]', 1,
                                            array_key_exists('supplier_custom_field_3', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_3')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[supplier_custom_field_4]', 1,
                                            array_key_exists('supplier_custom_field_4', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_4')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane @if(!$get_permissions['property_module']) hide @endif" id="property_customer">
                        <h3>@lang('lang_v1.select_the_field_you_want_in_adding_contact')</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_type]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.type')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_name]', 1,
                                            1, ['class' =>
                                            'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.name')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_contact_id]', 1,
                                            1, ['class'
                                            => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.contact_id')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_tax_number]', 1,
                                            array_key_exists('property_customer_tax_number', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.tax_nic_passport_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_opening_balance]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.opening_balance')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_pay_term]', 1,
                                            array_key_exists('property_customer_pay_term', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.pay_term')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_transaction_date]', 1,
                                            1,
                                            ['class' => 'input-icheck not_change', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.transaction_date')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_customer_group]', 1,
                                            array_key_exists('property_customer_customer_group', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.customer_group')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_password]', 1,
                                            1,
                                            ['class'
                                            => 'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.password')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_confirm_password]', 1,
                                            1,
                                            ['class' => 'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.confirm_password')}}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_email]', 1,
                                            1,
                                            ['class' =>
                                            'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.email')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_mobile]', 1,
                                            1,
                                            ['class' =>
                                            'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.mobile')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_alternate_contact_number]', 1,
                                            1,
                                            ['class' => 'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.alternate_contact_number')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_landline]', 1,
                                            1,
                                            ['class' => 'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.landline')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_address]', 1,
                                            1,
                                            ['class' => 'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.address')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_city]', 1,
                                            1, ['class'
                                            =>
                                            'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.city')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_state]', 1,
                                            1,
                                            ['class' =>
                                            'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.state')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_country]', 1,
                                            1,
                                            ['class' =>
                                            'input-icheck', 'disabled', 'checked']); !!}
                                            {{__('lang_v1.country')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_landmark]', 1,
                                            array_key_exists('property_customer_landmark', $business->contact_fields ?? []),
                                            ['class'
                                            => 'input-icheck']); !!}
                                            {{__('lang_v1.landmark')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_custom_field_1]', 1,
                                            array_key_exists('property_customer_custom_field_1', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_1')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_custom_field_2]', 1,
                                            array_key_exists('property_customer_custom_field_2', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_2')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_custom_field_3]', 1,
                                            array_key_exists('property_customer_custom_field_3', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_3')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('contact_fields[property_customer_custom_field_4]', 1,
                                            array_key_exists('property_customer_custom_field_4', $business->contact_fields ??
                                            []),
                                            ['class' => 'input-icheck']); !!}
                                            {{__('lang_v1.custom_field_4')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					@if(!empty($business_settings->captch_site_key))
						<div class="col-md-12">
                            <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
                                <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
                            </div>
						@endif
                </div>
            </div>
        </div>
    </div>
</div>