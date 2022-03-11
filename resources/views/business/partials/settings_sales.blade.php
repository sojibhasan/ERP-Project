<div class="pos-tab-content  @if($get_permissions['property_module'] == 1) hide  @endif">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_discount', __('business.default_sales_discount') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::text('default_sales_discount', @num_format($business->default_sales_discount), ['class' => 'form-control input_number']); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_tax', __('business.default_sales_tax') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('default_sales_tax', $tax_rates, $business->default_sales_tax, ['class' => 'form-control select2','placeholder' => __('business.default_sales_tax'), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
        </div>
        <!-- <div class="clearfix"></div> -->

        {{--<div class="col-sm-12 hide">
            <div class="form-group">
                {!! Form::label('sell_price_tax', __('business.sell_price_tax') . ':') !!}
                <div class="input-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="includes" 
                            class="input-icheck" @if($business->sell_price_tax == 'includes') {{'checked'}} @endif> Includes the Sale Tax
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="excludes" 
                            class="input-icheck" @if($business->sell_price_tax == 'excludes') {{'checked'}} @endif>Excludes the Sale Tax (Calculate sale tax on Selling Price provided in Add Purchase)
                        </label>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('sales_cmsn_agnt', $commission_agent_dropdown, $business->sales_cmsn_agnt, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('item_addition_method', __('lang_v1.sales_item_addition_method') . ':') !!}
                {!! Form::select('item_addition_method', [ 0 => __('lang_v1.add_item_in_new_row'), 1 =>  __('lang_v1.increase_item_qty')], $business->item_addition_method, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('service_addition_method', __('lang_v1.service_item_addition_method') . ':') !!}
                {!! Form::select('service_addition_method', [ 0 => __('lang_v1.add_service_in_new_row'), 1 =>  __('lang_v1.increase_item_qty')], $business->service_addition_method, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_msp]', 1,  
                        !empty($pos_settings['enable_msp']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.sale_price_is_minimum_sale_price' ) }} 
                  </label>
                  @if(!empty($help_explanations['sale_price_is_minimum_selling_price'])) @show_tooltip($help_explanations['sale_price_is_minimum_selling_price']) @endif
                </div>
            </div>
        </div>
        {{-- <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_enter_any_price]', 1,  
                        !empty($pos_settings['enable_enter_any_price']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_enter_any_price' ) }} 
                  </label>
                  <!-- @show_tooltip(__('lang_v1.minimum_sale_price_help')) -->
                </div>
            </div>
        </div> --}}
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[price_later_sales]', 1,  
                        !empty($pos_settings['price_later_sales']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.price_later' ) }} 
                  </label>
                  @if(!empty($help_explanations['price_later_sales'])) @show_tooltip($help_explanations['price_later_sales']) @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[allow_overselling]', 1,  
                        !empty($pos_settings['allow_overselling']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.allow_overselling' ) }} 
                  </label>
                  @if(!empty($help_explanations['allow_overselling'])) @show_tooltip($help_explanations['allow_overselling']) @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[sold_product_list]', 1,  
                        !empty($pos_settings['sold_product_list']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.sold_product_list' ) }} 
                  </label>
                  @if(!empty($help_explanations['sold_product_list_in_the_register_report'])) @show_tooltip($help_explanations['sold_product_list_in_the_register_report']) @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_line_discount]', 1,  
                        !empty($pos_settings['enable_line_discount']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_line_discount' ) }} 
                  </label>
                  @if(!empty($help_explanations['enable_line_discount'])) @show_tooltip($help_explanations['enable_line_discount']) @endif
                </div>
            </div>
        </div>
        @can('edit_product_price_below_purchase_price')
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_below_cost_price]', 1,  
                        !empty($pos_settings['enable_below_cost_price']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_below_cost_price' ) }} 
                  </label>
                  @if(!empty($help_explanations['below_cost_price'])) @show_tooltip($help_explanations['below_cost_price']) @endif
                </div>
            </div>
        </div>
        @endcan
    </div>
    @php
        $tc_sale_and_pos = DB::table('site_settings')->where('id', 1)->select('tc_sale_and_pos')->first()->tc_sale_and_pos;
    @endphp
    @if($tc_sale_and_pos == 1)
    <div class="row">
        <div class="col-md-6" id="lp_title">
            <div class="form-group">
                <label>Terms & Condition for the Sales / POS invoice</label>
                <textarea id="tc_sale_and_pos_text" name="pos_settings[tc_sale_and_pos_text]"></textarea>
            </div>
        </div>
    </div>
    @endif
</div>
