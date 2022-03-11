<div class="pos-tab-content">
     <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_prefix = '';
                    if(!empty($business->ref_no_prefixes['purchase'])){
                        $purchase_prefix = $business->ref_no_prefixes['purchase'];
                    }
                    $purchase_starting_number = '';
                    if(!empty($business->ref_no_starting_number['purchase'])){
                        $purchase_starting_number = $business->ref_no_starting_number['purchase'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase]', __('lang_v1.purchase_order') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[purchase]', $purchase_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[purchase]', $purchase_starting_number, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_return = '';
                    if(!empty($business->ref_no_prefixes['purchase_return'])){
                        $purchase_return = $business->ref_no_prefixes['purchase_return'];
                    }
                    $purchase_return_stating_no = '';
                    if(!empty($business->ref_no_starting_number['purchase_return'])){
                        $purchase_return_stating_no = $business->ref_no_starting_number['purchase_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_return]', __('lang_v1.purchase_return') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[purchase_return]', $purchase_return, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[purchase_return]',  $purchase_return_stating_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $stock_transfer_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_transfer'])){
                        $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
                    }
                    $stock_transfer_stating_number = '';
                    if(!empty($business->ref_no_starting_number['stock_transfer'])){
                        $stock_transfer_stating_number = $business->ref_no_starting_number['stock_transfer'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_transfer]', __('lang_v1.stock_transfer') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[stock_transfer]', $stock_transfer_stating_number, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $stock_adjustment_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                        $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
                    }
                    $stock_adjustment_starting_number = '';
                    if(!empty($business->ref_no_starting_number['stock_adjustment'])){
                        $stock_adjustment_starting_number = $business->ref_no_starting_number['stock_adjustment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_adjustment]', __('stock_adjustment.stock_adjustment') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[stock_adjustment]', $stock_adjustment_starting_number, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $sell_return_prefix = '';
                    if(!empty($business->ref_no_prefixes['sell_return'])){
                        $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
                    }
                    $sell_return_starting_no = '';
                    if(!empty($business->ref_no_starting_number['sell_return'])){
                        $sell_return_starting_no = $business->ref_no_starting_number['sell_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_return]', __('lang_v1.sell_return') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[sell_return]', $sell_return_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[sell_return]',  $sell_return_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expenses_prefix = '';
                    if(!empty($business->ref_no_prefixes['expense'])){
                        $expenses_prefix = $business->ref_no_prefixes['expense'];
                    }
                    $expenses_starting_no = '';
                    if(!empty($business->ref_no_starting_number['expense'])){
                        $expenses_starting_no = $business->ref_no_starting_number['expense'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense]', __('expense.expenses') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[expense]', $expenses_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[expense]', $expenses_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $contacts_prefix = '';
                    if(!empty($business->ref_no_prefixes['contacts'])){
                        $contacts_prefix = $business->ref_no_prefixes['contacts'];
                    }
                    $contacts_starting_no = '';
                    if(!empty($business->ref_no_starting_number['contacts'])){
                        $contacts_starting_no = $business->ref_no_starting_number['contacts'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[contacts]', __('contact.contacts') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[contacts]', $contacts_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[contacts]', $contacts_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_payment = '';
                    if(!empty($business->ref_no_prefixes['purchase_payment'])){
                        $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
                    }
                    $purchase_payment_starting_no = '';
                    if(!empty($business->ref_no_starting_number['purchase_payment'])){
                        $purchase_payment_starting_no = $business->ref_no_starting_number['purchase_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_payment]', __('lang_v1.purchase_payment') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[purchase_payment]', $purchase_payment, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[purchase_payment]', $purchase_payment_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $sell_payment = '';
                    if(!empty($business->ref_no_prefixes['sell_payment'])){
                        $sell_payment = $business->ref_no_prefixes['sell_payment'];
                    }
                    $sell_payment_starting_no = '';
                    if(!empty($business->ref_no_starting_number['sell_payment'])){
                        $sell_payment_starting_no = $business->ref_no_starting_number['sell_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_payment]', __('lang_v1.sell_payment') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[sell_payment]', $sell_payment, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[sell_payment]', $sell_payment_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expense_payment = '';
                    if(!empty($business->ref_no_prefixes['expense_payment'])){
                        $expense_payment = $business->ref_no_prefixes['expense_payment'];
                    }
                    $expense_payment_starting_no = '';
                    if(!empty($business->ref_no_starting_number['expense_payment'])){
                        $expense_payment_starting_no = $business->ref_no_starting_number['expense_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense_payment]', __('lang_v1.expense_payment') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[expense_payment]', $expense_payment, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[expense_payment]', $expense_payment_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $business_location_prefix = '';
                    if(!empty($business->ref_no_prefixes['business_location'])){
                        $business_location_prefix = $business->ref_no_prefixes['business_location'];
                    }
                    $business_location_starting_number = '';
                    if(!empty($business->ref_no_starting_number['business_location'])){
                        $business_location_starting_number = $business->ref_no_starting_number['business_location'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[business_location]', __('business.business_location') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[business_location]', $business_location_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[business_location]',  $business_location_starting_number, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $username_prefix = !empty($business->ref_no_prefixes['username']) ? $business->ref_no_prefixes['username'] : '';
                    $username_strating_no = !empty($business->ref_no_starting_number['username']) ? $business->ref_no_starting_number['username'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[username]', __('business.username') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[username]', $username_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[username]',  $username_strating_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $subscription_prefix = !empty($business->ref_no_prefixes['subscription']) ? $business->ref_no_prefixes['subscription'] : '';
                    $subscription_starting_no = !empty($business->ref_no_starting_number['subscription']) ? $business->ref_no_starting_number['subscription'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[subscription]', __('lang_v1.subscription_no') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[subscription]', $subscription_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[subscription]', $subscription_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $customer_prefix = !empty($business->ref_no_prefixes['customer']) ? $business->ref_no_prefixes['customer'] : '';
                    $customer_starting_no = !empty($business->ref_no_starting_number['customer']) ? $business->ref_no_starting_number['customer'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[customer]', __('lang_v1.customer') . ':') !!}
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                
                {!! Form::text('ref_no_prefixes[customer]', $customer_prefix, ['class' => 'form-control']); !!}
                </div>
                 <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[customer]', $customer_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $supplier_prefix = !empty($business->ref_no_prefixes['supplier']) ? $business->ref_no_prefixes['supplier'] : '';
                    $supplier_starting_no = !empty($business->ref_no_starting_number['supplier']) ? $business->ref_no_starting_number['supplier'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[supplier]', __('lang_v1.supplier') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[supplier]', $supplier_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[supplier]', $supplier_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $settlement_prefix = !empty($business->ref_no_prefixes['settlement']) ? $business->ref_no_prefixes['settlement'] : '';
                    $settlement_starting_no = !empty($business->ref_no_starting_number['settlement']) ? $business->ref_no_starting_number['settlement'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[settlement]', __('lang_v1.settlement') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[settlement]', $settlement_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[settlement]', $settlement_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $excess_commission_prefix = !empty($business->ref_no_prefixes['excess_commission']) ? $business->ref_no_prefixes['excess_commission'] : '';
                    $excess_commission_starting_no = !empty($business->ref_no_starting_number['excess_commission']) ? $business->ref_no_starting_number['excess_commission'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[excess_commission]', __('lang_v1.excess_commission') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[excess_commission]', $excess_commission_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[excess_commission]', $excess_commission_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $shortage_recover_prefix = !empty($business->ref_no_prefixes['shortage_recover']) ? $business->ref_no_prefixes['shortage_recover'] : '';
                    $shortage_recover_starting_no = !empty($business->ref_no_starting_number['shortage_recover']) ? $business->ref_no_starting_number['shortage_recover'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[shortage_recover]', __('lang_v1.shortage_recover') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[shortage_recover]', $shortage_recover_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[shortage_recover]', $shortage_recover_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $security_deposit_prefix = !empty($business->ref_no_prefixes['security_deposit']) ? $business->ref_no_prefixes['security_deposit'] : '';
                    $security_deposit_starting_no = !empty($business->ref_no_starting_number['security_deposit']) ? $business->ref_no_starting_number['security_deposit'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[security_deposit]', __('lang_v1.security_deposit') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[security_deposit]', $security_deposit_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[security_deposit]', $security_deposit_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['property_module'] == 1) hide  @endif">
            <div class="form-group">
                @php
                    $refund_security_deposit_prefix = !empty($business->ref_no_prefixes['refund_security_deposit']) ? $business->ref_no_prefixes['refund_security_deposit'] : '';
                    $refund_security_deposit_starting_no = !empty($business->ref_no_starting_number['refund_security_deposit']) ? $business->ref_no_starting_number['refund_security_deposit'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[refund_security_deposit]', __('lang_v1.refund_security_deposit') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[refund_security_deposit]', $refund_security_deposit_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[refund_security_deposit]', $refund_security_deposit_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4  @if($get_permissions['fleet_module'] == 0) hide  @endif">
            <div class="form-group">
                @php
                    $employee_no_prefix = !empty($business->ref_no_prefixes['employee_no']) ? $business->ref_no_prefixes['employee_no'] : '';
                    $employee_no_starting_no = !empty($business->ref_no_starting_number['employee_no']) ? $business->ref_no_starting_number['employee_no'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[employee_no]', __('lang_v1.employee_no') . ':') !!}
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.prefix')
					</span>
                {!! Form::text('ref_no_prefixes[employee_no]', $employee_no_prefix, ['class' => 'form-control']); !!}
                </div>
                <div class="input-group">
					<span class="input-group-addon">
						@lang('lang_v1.starting_number')
					</span>
                {!! Form::text('ref_no_starting_number[employee_no]', $employee_no_starting_no, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
    </div>
</div>