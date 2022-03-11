<div class="pos-tab-content  @if($get_permissions['property_module'] == 1) hide  @endif">
<div class="row well">
    <div class="col-sm-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! Form::checkbox('enable_rp', 1, $business->enable_rp , 
                [ 'class' => 'input-icheck', 'id' => 'enable_rp']); !!} {{ __( 'lang_v1.enable_rp' ) }}
                </label>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-12">
        <h4>@lang('lang_v1.earning_points_setting'):</h4>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('rp_name', __('lang_v1.rp_name') . ':') !!}
            {!! Form::text('rp_name', $business->rp_name, ['class' => 'form-control','placeholder' => __('lang_v1.rp_name')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('amount_for_unit_rp', __('lang_v1.amount_for_unit_rp') . ':') !!}  @if(!empty($help_explanations['amount_spend_for_unit_point'])) @show_tooltip($help_explanations['amount_spend_for_unit_point']) @endif
            {!! Form::text('amount_for_unit_rp', @num_format($business->amount_for_unit_rp), ['class' => 'form-control input_number','placeholder' => __('lang_v1.amount_for_unit_rp')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('min_order_total_for_rp', __('lang_v1.min_order_total_for_rp') . ':') !!} @if(!empty($help_explanations['minimum_order_total_to_earn_reward'])) @show_tooltip($help_explanations['minimum_order_total_to_earn_reward']) @endif
            {!! Form::text('min_order_total_for_rp', @num_format($business->min_order_total_for_rp), ['class' => 'form-control input_number','placeholder' => __('lang_v1.min_order_total_for_rp')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('max_rp_per_order', __('lang_v1.max_rp_per_order') . ':') !!}@if(!empty($help_explanations['maximum_point_per_order'])) @show_tooltip($help_explanations['maximum_point_per_order']) @endif
            {!! Form::number('max_rp_per_order', $business->max_rp_per_order, ['class' => 'form-control','placeholder' => __('lang_v1.max_rp_per_order')]); !!}
        </div>
    </div>
   </div>
   <div class="row well">
    <div class="col-sm-12">
        <h4>@lang('lang_v1.redeem_points_setting'):</h4>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('redeem_amount_per_unit_rp', __('lang_v1.redeem_amount_per_unit_rp') . ':') !!}@if(!empty($help_explanations['redeem_amount_per_unit_point'])) @show_tooltip($help_explanations['redeem_amount_per_unit_point']) @endif
            {!! Form::text('redeem_amount_per_unit_rp', @num_format($business->redeem_amount_per_unit_rp), ['class' => 'form-control input_number','placeholder' => __('lang_v1.redeem_amount_per_unit_rp')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('min_order_total_for_redeem', __('lang_v1.min_order_total_for_redeem') . ':') !!} @if(!empty($help_explanations['minimum_order_total_to_redeem_points'])) @show_tooltip($help_explanations['minimum_order_total_to_redeem_points']) @endif
            {!! Form::text('min_order_total_for_redeem', @num_format($business->min_order_total_for_redeem), ['class' => 'form-control input_number','placeholder' => __('lang_v1.min_order_total_for_redeem')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('min_redeem_point', __('lang_v1.min_redeem_point') . ':') !!} @if(!empty($help_explanations['minimum_redeem_point'])) @show_tooltip($help_explanations['minimum_redeem_point']) @endif
            {!! Form::number('min_redeem_point', $business->min_redeem_point, ['class' => 'form-control','placeholder' => __('lang_v1.min_redeem_point')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('max_redeem_point', __('lang_v1.max_redeem_point') . ':') !!} @if(!empty($help_explanations['maximum_redeem_point_per_order'])) @show_tooltip($help_explanations['maximum_redeem_point_per_order']) @endif
            {!! Form::number('max_redeem_point', $business->max_redeem_point, ['class' => 'form-control', 'placeholder' => __('lang_v1.max_redeem_point')]); !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('rp_expiry_period', __('lang_v1.rp_expiry_period') . ':') !!} @if(!empty($help_explanations['reward_point_expiry_period'])) @show_tooltip($help_explanations['reward_point_expiry_period']) @endif
            <div class="input-group">
                {!! Form::number('rp_expiry_period', $business->rp_expiry_period, ['class' => 'form-control','placeholder' => __('lang_v1.rp_expiry_period')]); !!}
                <span class="input-group-addon">-</span>
                {!! Form::select('rp_expiry_type', ['month' => __('lang_v1.month'), 'year' => __('lang_v1.year')], $business->rp_expiry_type, ['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
    </div>
</div>