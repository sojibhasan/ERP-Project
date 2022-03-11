<div class="pos-tab-content @if($get_permissions['property_module'] == 1) hide  @endif">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sku_prefix', __('business.sku_prefix') . ':') !!}
                 {!! Form::text('sku_prefix', $business->sku_prefix, ['class' => 'form-control text-uppercase']); !!}
            </div>
        </div>

        @if(!config('constants.disable_expiry', true))
        <div class="col-sm-4">
            {!! Form::label('enable_product_expiry', __( 'product.enable_product_expiry' ) . ':') !!}
            @show_tooltip(__('lang_v1.tooltip_enable_expiry'))

            <div class="input-group">
                <span class="input-group-addon">
                    {!! Form::checkbox('enable_product_expiry', 1, $business->enable_product_expiry ); !!} 
                </span>

                <select class="form-control" id="expiry_type"
                    name="expiry_type" 
                    @if(!$business->enable_product_expiry) disabled @endif>
                    <option value="add_expiry" @if($business->expiry_type == 'add_expiry') selected @endif>
                        {{__('lang_v1.add_expiry')}}
                    </option>
                  <option value="add_manufacturing" @if($business->expiry_type == 'add_manufacturing') selected @endif>{{__('lang_v1.add_manufacturing_auto_expiry')}}</option>
                </select>
            </div>
        </div>

        <div class="col-sm-4 @if(!$business->enable_product_expiry) hide @endif" id="on_expiry_div">
            <div class="form-group">
                <div class="multi-input">
                    {!! Form::label('on_product_expiry', __('lang_v1.on_product_expiry') . ':') !!}
                    @show_tooltip(__('lang_v1.tooltip_on_product_expiry'))
                    <br>

                    {!! Form::select('on_product_expiry',     ['keep_selling'=>__('lang_v1.keep_selling'), 'stop_selling'=>__('lang_v1.stop_selling') ], $business->on_product_expiry, ['class' => 'form-control pull-left', 'style' => 'width:60%;']); !!}

                    @php
                        $disabled = '';
                        if($business->on_product_expiry == 'keep_selling'){
                            $disabled = 'disabled';
                        }
                    @endphp

                    {!! Form::number('stop_selling_before', $business->stop_selling_before, ['class' => 'form-control pull-left', 'placeholder' => 'stop n days before', 'style' => 'width:40%;', $disabled, 'required', 'id' => 'stop_selling_before']); !!}
                </div>
            </div>
        </div>

        @endif
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_brand', 1, $business->enable_brand, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_brand' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_category', 1, $business->enable_category, [ 'class' => 'input-icheck', 'id' => 'enable_category']); !!} {{ __( 'lang_v1.enable_category' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4 enable_sub_category @if($business->enable_category != 1) hide @endif">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_sub_category', 1, $business->enable_sub_category, [ 'class' => 'input-icheck', 'id' => 'enable_sub_category']); !!} {{ __( 'lang_v1.enable_sub_category' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_price_tax', 1, $business->enable_price_tax, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_price_tax' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_unit', __('lang_v1.default_unit') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-balance-scale"></i>
                    </span>
                    {!! Form::select('default_unit', $units_dropdown, $business->default_unit, ['class' => 'form-control select2', 'style' => 'width: 100%;' ]); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_sub_units', 1, $business->enable_sub_units, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_sub_units' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.sub_units_tooltip'))
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_racks', 1, $business->enable_racks, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_racks' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.tooltip_enable_racks'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_row', 1, $business->enable_row, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_row' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_position', 1, $business->enable_position, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_position' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('show_avai_qty_in_qr_catalogue', 1, $business->show_avai_qty_in_qr_catalogue, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_avai_qty_in_qr_catalogue' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('show_in_catalogue_page', 1, $business->show_in_catalogue_page, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_in_catalogue_page' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('common_settings[enable_product_warranty]', 1, !empty($common_settings['enable_product_warranty']) ? true : false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_product_warranty' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-4">
          {!! Form::label('pos_invoive_sale', __('business.pos_invoive_sale') . ':') !!}
          <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('search_product_settings[enable_code]', 1, !empty($search_product_settings->enable_code) ? true : false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'business.enable_code' ) }}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('search_product_settings[enable_rack_number]', 1, !empty($search_product_settings->enable_rack_number) ? true : false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'business.enable_rack_number' ) }}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('search_product_settings[enable_qty]', 1, !empty($search_product_settings->enable_qty) ? true : false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'business.enable_qty' ) }}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('search_product_settings[enable_product_cost]', 1, !empty($search_product_settings->enable_product_cost) ? true : false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'business.enable_product_cost' ) }}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('search_product_settings[enable_product_supplier]', 1, !empty($search_product_settings->enable_product_supplier) ? true : false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'business.enable_product_supplier' ) }}
                </label>
              </div>
          </div>
      </div>



    </div>
</div>