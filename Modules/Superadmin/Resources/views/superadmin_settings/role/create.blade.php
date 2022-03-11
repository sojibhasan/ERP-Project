@extends('layouts.app')
@section('title', __('role.add_role'))
@section('content')
<style>
  .content h4 label {
    font-weight: inherit !important;
  }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.add_role' )</h1>
</section>
<section class="content-header">
  @include('layouts.partials.search_settings')
</section>
<!-- Main content -->
<section class="content pos-tab-container">
  @component('components.widget', ['class' => 'box-primary'])
  {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]);
        !!}
      </div>
    </div>
  </div>
  @if(in_array('service_staff', $enabled_modules))
  <div class="row">
    <div class="col-md-2">
      <h4> <label>@lang( 'lang_v1.user_type' )</label></h4>
    </div>
    <div class="col-md-9 col-md-offset-1">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('is_service_staff', 1, false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
          </label>
          @show_tooltip(__('restaurant.tooltip_service_staff'))
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-md-3">
      <label>@lang( 'user.permissions' ):</label>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.user' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'user.roles' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.supplier' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.customer' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'business.product' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.opening_stock', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'view_purchase_price', false,['class' => 'input-icheck']); !!}
            {{ __('lang_v1.view_purchase_price') }}
          </label>
          @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.price_section', false,['class' => 'input-icheck']); !!}
            {{ __('lang_v1.product.price_section') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.purchase' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.payments', false,['class' => 'input-icheck']); !!}
            {{ __('lang_v1.purchase.payments') }}
          </label>
          @show_tooltip(__('lang_v1.purchase_payments'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.update_status', false,['class' => 'input-icheck']); !!}
            {{ __('lang_v1.update_status') }}
          </label>
        </div>
      </div>

    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.expense' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.add_payment', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.add_payment' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'sale.sale' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'direct_sell.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.direct_sell.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_drafts', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_drafts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_quotations', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_quotations' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'view_own_sell_only', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.payments', false, ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.sell.payments') }}
          </label>
          @show_tooltip(__('lang_v1.sell_payments'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen', false, ['class' =>
            'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_from_sale_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen', false, ['class' =>
            'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_from_pos_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_below_purchase_price', false, ['class' =>
            'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_below_purchase_price') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen', false, ['class' =>
            'input-icheck']); !!}
            {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen', false, ['class' =>
            'input-icheck']); !!}
            {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'discount.access', false, ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.discount.access') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'access_shipping', false, ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.access_shipping') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'pos_page_return', false, ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.pos_page_return') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'status_order', false, ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.status_order') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang('visitors.visitor_registration' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.business.name.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_business_name_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.business.name.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_business_name_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.date.time.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_date_time_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.date.time.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_date_time_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.visited.date.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_visited_date_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.visited.date.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_visited_date_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.mobile.number.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_mobile_number_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.mobile.number.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_mobile_number_enable' ) }}

          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.land.number.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_land_number_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.land.number.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_land_number_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.name.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_name_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.name.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_name_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.address.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_address_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.add', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.add', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.details.required', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_details_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.details.enable', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_details_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.settings.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.settings.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_edit' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.brand' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.tax_rate' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.unit' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'category.category' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'lang_v1.crm' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.update', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-12">
      <h4><label>@lang( 'role.report' )</label></h4>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.product_reports' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'stock_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'stock_adjustment_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'item_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.item_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_purchase_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_purchase_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_sell_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_sell_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_transaction_report.view',
            false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_transaction_report.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.payment_status_reports' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase_payment_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_payment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell_payment_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell_payment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'outstanding_received_report.view',
            false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.outstanding_received_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'aging_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.aging_report.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.management_reports' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'daily_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'daily_summary_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_summary_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'register_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'profit_loss_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'credit_status.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.credit_status.view' ) }}
          </label>
        </div>
      </div>

    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.verification_reports' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'monthly_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.monthly_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'comparison_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.comparison_report.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.activity_report' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sales_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase_and_slae_report.view',
            false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_and_slae_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sales_representative.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-1">
      <h4><label>@lang( 'role.contact_report' )</label></h4>
    </div>
    <div class="col-md-2">

    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'contact_report.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.contact_report.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-1">
      <h4><label>@lang( 'role.trending_products' )</label></h4>
    </div>
    <div class="col-md-2">

    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'trending_products.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_products.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-1">
      <h4><label>@lang( 'role.user_activity' )</label></h4>
    </div>
    <div class="col-md-2">

    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user_activity.view',false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user_activity.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.settings' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'business_settings.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'barcode_settings.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'invoice_settings.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'backup', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.backup' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.unfinished_form' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.purchase', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.purchase' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.sale', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.sale' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.pos', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.pos' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.stock_adjustment', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_adjustment' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.stock_transfer', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_transfer' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.expense', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.expense' ) }}
          </label>
        </div>
      </div>



    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-3">
      <h4> <label>@lang( 'role.dashboard' ) @show_tooltip(__('tooltip.dashboard_permission'))</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'dashboard.data', true,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-3">
      <h4> <label>PayRoll</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'payday', false,
            [ 'class' => 'input-icheck']); !!} PayRoll
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'account.account' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_accounts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.link_account', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.link_account' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.reconcile', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.reconcile' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'contact.customer_statement' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'enable_separate_customer_statement_no', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'contact.enable_separate_customer_statement_no' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_customer_statement', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'contact.edit_customer_statement' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'lang_v1.customer_reference' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer_reference.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_reference.edit' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @if($get_permissions['mpcs_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.MPCS' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'mpcs.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f9c_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f9c_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f15a9abc_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f15a9abc_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f16a_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f16a_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f21c_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21c_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f17_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f17_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f14b_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f14b_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f20_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f20_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f21_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f22_stock_taking_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f22_stock_taking_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_f22_stock_Taking_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f22_stock_Taking_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_f17_form', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f17_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'mpcs_form_settings', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs_form_settings' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_opening_values', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.list_opening_values' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif
  @if($get_permissions['ran_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.ran' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'ran.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.ran.access' ) }}
          </label>
        </div>
      </div>

    </div>
  </div>
  <hr>
  @endif

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.catalogue_qr' )</label></h4>
    </div>
    <div class="col-md-2">

    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'catalogue.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.catalogue.access' ) }}
          </label>
        </div>
      </div>

    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.repair' )</label></h4>
    </div>
    <div class="col-md-2">

    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'repair.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.repair.access' ) }}
          </label>
        </div>
      </div>

    </div>
  </div>
  <hr>

  @if($get_permissions['enable_petro_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.petro' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'petro.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.petro.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'fuel_tank.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.fuel_tank.edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'meter_resetting_tab', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.meter_resetting_tab' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'add_dip_resetting', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_dip_resetting' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_other_income_prices', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_other_income_prices' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'daily_collection.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_collection.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'petro::lang.settlement' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'settlement.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_settlement' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'reset_dip', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.reset_dip' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.pump_operator' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'pum_operator.active_inactive', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.pum_operator.active_inactive' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'pump_operator.dashboard', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.pump_operator.dashboard' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['issue_customer_bill'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.issue_customer_bill' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.add', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif
  @if($get_permissions['customer_settings'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.customer_settings' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer_settings.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'approve_sell_over_limit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.approve_sell_over_limit' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['tasks_management'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.tasks_management' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tasks_management.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tasks_management.tasks', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.tasks' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tasks_management.reminder', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.reminder' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['member_registration'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.member_registration' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'member_registration.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.member_registration.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'add_remarks', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_remarks' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'update_status_of_issue', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.update_status_of_issue' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif
  @if($get_permissions['leads_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.leads' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.view', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.import', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.import' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.settings', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.settings' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
      <h4> <label>@lang( 'role.day_count' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('day_count')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'day_count', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.day_count') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['leads_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.sms' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sms.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.access' ) }}
          </label>
        </div>
      </div>

      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sms.list', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.list' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif


  @if(in_array('tables', $enabled_modules) && in_array('service_staff', $enabled_modules) )
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'restaurant.bookings' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crud_all_bookings', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crud_own_bookings', false,
            [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif
  <div class="row">
    <div class="col-md-3">
      <h4> <label>@lang( 'lang_v1.access_selling_price_groups' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'access_default_selling_price', true,
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
          </label>
        </div>
      </div>
      @if(count($selling_price_groups) > 0)
      @foreach($selling_price_groups as $selling_price_group)
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, false,
            [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
          </label>
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>
  <hr>

  @if(auth()->user()->can('product.set_min_sell_price'))
  <div class="row">
    <div class="col-md-3">
      <h4> <label>@lang( 'lang_v1.min_sell_price' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('product.set_min_sell_price')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.set_min_sell_price', false,
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.min_sell_price') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <hr>
  @endif

  @if(auth()->user()->can('sales-commission-agents.create'))
  <div class="row">
    <div class="col-md-3">
      <h4> <label>@lang( 'lang_v1.sales_commission_agents_create' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('sales-commission-agents.create')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sales-commission-agents.create', false,
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.sales_commission_agents_create') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'lang_v1.hr' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      @can('hr::lang.access')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.access', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.access') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.employee', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.employee') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.attendance', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.attendance') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.payroll', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.payroll') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.reports', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.reports') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.notice_board', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.notice_board') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.settings', false,
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.settings') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.employee' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'employee.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.employee.edit') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.attendance' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'attendance.approve_reject_lo', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.attendance.approve_reject_lo') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4> <label>@lang( 'role.leave_request' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">
        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
      </div>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leave_request.approve_reject', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.approve_reject') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leave_request.edit', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.edit') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leave_request.delete', false,
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.delete') }}
          </label>
        </div>
      </div>
    </div>
    <hr>
    @endif

    @if(auth()->user()->can('day_end.view') || auth()->user()->can('day_end.bypass'))
    <div class="row check_group">
      <div class="col-md-1">
        <h4> <label>@lang( 'lang_v1.day_end' )</label></h4>
      </div>
      <div class="col-md-2">
        <div class="checkbox">

          <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

        </div>
      </div>
      <div class="col-md-9">
        @can('day_end.view')
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'day_end.view', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_view') }}
            </label>
          </div>
        </div>
        @endcan
        @can('day_end.bypass')
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'day_end.bypass', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_bypass') }}
            </label>
          </div>
        </div>
        @endcan
      </div>
    </div>
    <hr>
    @endif

    @if($get_permissions['upload_images'])
    @if(auth()->user()->can('upload_images'))
    <div class="row">
      <div class="col-md-3">
        <h4> <label>@lang( 'lang_v1.upload_images' )</label></h4>
      </div>
      <div class="col-md-9">
        @can('upload_images')
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'upload_images', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.upload_images') }}
            </label>
          </div>
        </div>
        @endcan
      </div>
    </div>
    @endif
    @endif

    @if($get_permissions['sms_enable'])
    @if(auth()->user()->can('sms.view'))
    <div class="row">
      <div class="col-md-3">
        <h4> <label>@lang( 'lang_v1.sms' )</label></h4>
      </div>
      <div class="col-md-9">
        @can('sms.view')
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'sms.view', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.sms_view') }}
            </label>
          </div>
        </div>
        @endcan
      </div>
    </div>
    @endif
    @endif


    @if($get_permissions['enable_restaurant'])
    <div class="row">
      <div class="col-md-3">
        <h4> <label>@lang( 'lang_v1.restaurant' )</label></h4>
      </div>
      <div class="col-md-9">
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'restaurant.access', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_restaurant') }}
            </label>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if( $get_permissions['cache_clear'])
    <div class="row">
      <div class="col-md-3">
        <h4> <label>@lang( 'lang_v1.clear_cache' )</label></h4>
      </div>
      <div class="col-md-9">
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'cache_clear', false,
              [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.clear_cache') }}
            </label>
          </div>
        </div>
      </div>
    </div>
    @endif

    @include('role.partials.module_permissions')
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.save' )</button>
      </div>
    </div>

    {!! Form::close() !!}
    @endcomponent
</section>
<!-- /.content -->
@endsection