@extends('layouts.app')
@section('title', __('role.edit_role'))

@section('content')
<style>
  .content h4 label {
    font-weight: inherit !important;
  }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.edit_role' )</h1>
</section>
<section class="content-header">
  @include('layouts.partials.search_settings')
</section>
<!-- Main content -->
<section class="content pos-tab-container">
  @component('components.widget', ['class' => 'box-primary'])
  {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@update', [$role->id]), 'method' => 'PUT', 'id' => 'role_form' ]) !!}
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
        {!! Form::text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name) , ['class' =>
        'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
      </div>
    </div>
  </div>
  @if(in_array('service_staff', $enabled_modules))
  <div class="row">
    <div class="col-md-2">
      <h4><label>@lang( 'lang_v1.user_type' )</label></h4>
    </div>
    <div class="col-md-9 col-md-offset-1">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('is_service_staff', 1, $role->is_service_staff,
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
      <h4><label>@lang( 'role.user' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'user.view', in_array('user.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.create', in_array('user.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.update', in_array('user.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'user.delete', in_array('user.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'user.roles' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'roles.view', in_array('roles.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.create', in_array('roles.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.update', in_array('roles.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'roles.delete', in_array('roles.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.supplier' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'supplier.view', in_array('supplier.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.create', in_array('supplier.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.update', in_array('supplier.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'supplier.delete', in_array('supplier.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.customer' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'customer.view', in_array('customer.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.create', in_array('customer.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.update', in_array('customer.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'customer.delete', in_array('customer.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'business.product' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'product.view', in_array('product.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.create', in_array('product.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.update', in_array('product.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.delete', in_array('product.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.opening_stock', in_array('product.opening_stock',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'view_purchase_price', in_array('view_purchase_price',
            $role_permissions),['class' => 'input-icheck']); !!}
            {{ __('lang_v1.view_purchase_price') }}
          </label>
          @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.price_section', in_array('product.price_section',
            $role_permissions),['class' => 'input-icheck']); !!}
            {{ __('lang_v1.product.price_section') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.purchase' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.create', in_array('purchase.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.update', in_array('purchase.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.delete', in_array('purchase.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.payments', in_array('purchase.payments',
            $role_permissions),['class' => 'input-icheck']); !!}
            {{ __('lang_v1.purchase.payments') }}
          </label>
          @show_tooltip(__('lang_v1.purchase_payments'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase.update_status', in_array('purchase.update_status',
            $role_permissions),['class' => 'input-icheck']); !!}
            {{ __('lang_v1.update_status') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.expense' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'expense.create', in_array('expense.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.update', in_array('expense.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.delete', in_array('expense.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.add_payment', in_array('expense.add_payment',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.add_payment' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'sale.sale' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'sell.view', in_array('sell.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.create', in_array('sell.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.update', in_array('sell.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.delete', in_array('sell.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'direct_sell.access', in_array('direct_sell.access', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.direct_sell.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_drafts', in_array('list_drafts', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_drafts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_quotations', in_array('list_quotations', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_quotations' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'view_own_sell_only', in_array('view_own_sell_only', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell.payments', in_array('sell.payments', $role_permissions), ['class'
            => 'input-icheck']); !!}
            {{ __('lang_v1.sell.payments') }}
          </label>
          @show_tooltip(__('lang_v1.sell_payments'))
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen',
            in_array('edit_product_price_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_from_sale_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen',
            in_array('edit_product_price_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_from_pos_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_price_below_purchase_price',
            in_array('edit_product_price_below_purchase_price', $role_permissions), ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.edit_product_price_below_purchase_price') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen',
            in_array('edit_product_discount_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen',
            in_array('edit_product_discount_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'discount.access', in_array('discount.access', $role_permissions),
            ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.discount.access') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'access_shipping', in_array('access_shipping', $role_permissions),
            ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.access_shipping') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'pos_page_return', in_array('pos_page_return', $role_permissions),
            ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.pos_page_return') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'status_order', in_array('status_order', $role_permissions),
            ['class' => 'input-icheck']); !!}
            {{ __('lang_v1.status_order') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.brand' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'brand.view', in_array('brand.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.create', in_array('brand.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.update', in_array('brand.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'brand.delete', in_array('brand.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
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
            {!! Form::checkbox('permissions[]', 'visitor.registration.create',in_array('visitor.registration.create',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.view',in_array('visitor.registration.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.edit',in_array('visitor.registration.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.registration.delete',in_array('visitor.registration.delete',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]',
            'visitor.business.name.required',in_array('visitor.business.name.required', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_business_name_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.business.name.enable',in_array('visitor.business.name.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_business_name_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.date.time.required',in_array('visitor.date.time.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_date_time_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.date.time.enable',in_array('visitor.date.time.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_date_time_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]',
            'visitor.visited.date.required',in_array('visitor.visited.date.required', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_visited_date_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.visited.date.enable',in_array('visitor.visited.date.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_visited_date_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]',
            'visitor.mobile.number.required',in_array('visitor.mobile.number.required', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_mobile_number_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.mobile.number.enable',in_array('visitor.mobile.number.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_mobile_number_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.land.number.required',in_array('visitor.land.number.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_land_number_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.land.number.enable',in_array('visitor.land.number.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_land_number_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.name.required',in_array('visitor.name.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_name_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.name.enable',in_array('visitor.name.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_name_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.address.enable',in_array('visitor.address.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_address_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.required',in_array('visitor.district.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.details.required',in_array('visitor.details.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_details_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.enable',in_array('visitor.district.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.district.add',in_array('visitor.district.add',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_district_add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.required',in_array('visitor.town.required',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_required' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.enable',in_array('visitor.town.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.town.add',in_array('visitor.town.add', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_town_add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.details.enable',in_array('visitor.details.enable',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_details_enable' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.settings.view', in_array('visitor.settings.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'visitor.settings.edit', in_array('visitor.settings.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_edit' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <!-- end of visitor registration role -->
  <hr>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.tax_rate' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'tax_rate.view', in_array('tax_rate.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.create', in_array('tax_rate.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.update', in_array('tax_rate.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_rate.delete', in_array('tax_rate.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.unit' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'unit.view', in_array('unit.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.create', in_array('unit.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.update', in_array('unit.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unit.delete', in_array('unit.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'category.category' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'category.view', in_array('category.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.create', in_array('category.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.update', in_array('category.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'category.delete', in_array('category.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'lang_v1.crm' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'crm.view', in_array('crm.view', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.create', in_array('crm.create', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.update', in_array('crm.update', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.update' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crm.delete', in_array('crm.delete', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-1">
      <h4><label>@lang( 'role.report' )</label></h4>
    </div>
    <div class="col-md-2">
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'report.access', in_array('report.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.report.access' ) }}
          </label>
        </div>
      </div>
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
            {!! Form::checkbox('permissions[]', 'stock_report.view', in_array('stock_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'stock_adjustment_report.view',
            in_array('stock_adjustment_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'item_report.view', in_array('item_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.item_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_purchase_report.view',
            in_array('product_purchase_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_purchase_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_sell_report.view', in_array('product_sell_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_sell_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product_transaction_report.view',
            in_array('product_transaction_report.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'purchase_payment_report.view',
            in_array('purchase_payment_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_payment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sell_payment_report.view', in_array('sell_payment_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell_payment_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'outstanding_received_report.view',
            in_array('outstanding_received_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.outstanding_received_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'aging_report.view', in_array('aging_report.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'daily_report.view', in_array('daily_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'daily_summary_report.view', in_array('daily_summary_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_summary_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'register_report.view', in_array('register_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'profit_loss_report.view', in_array('profit_loss_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'credit_status.view', in_array('credit_status.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'monthly_report.view',in_array('monthly_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.monthly_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'comparison_report.view',in_array('comparison_report.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'sales_report.view', in_array('sales_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'purchase_and_slae_report.view',
            in_array('purchase_and_slae_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_and_slae_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense_report.view', in_array('expense_report.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sales_representative.view', in_array('sales_representative.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tax_report.view', in_array('tax_report.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'contact_report.view', in_array('contact_report.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'trending_products.view', in_array('trending_products.view',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'user_activity.view', in_array('user_activity.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.user_activity.view' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.settings' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'business_settings.access', in_array('business_settings.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'barcode_settings.access', in_array('barcode_settings.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'invoice_settings.access', in_array('invoice_settings.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'expense.access', in_array('expense.access', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'backup', in_array('backup', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.backup' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.unfinished_form' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'unfinished_form.purchase', in_array('unfinished_form.purchase',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.purchase' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.sale', in_array('unfinished_form.sale',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.sale' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.pos', in_array('unfinished_form.pos',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.pos' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.stock_adjustment',
            in_array('unfinished_form.stock_adjustment',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_adjustment' ) }}
          </label>
        </div>
      </div>

      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.stock_transfer',
            in_array('unfinished_form.stock_transfer',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_transfer' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'unfinished_form.expense', in_array('unfinished_form.expense',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.expense' ) }}
          </label>
        </div>
      </div>



    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-3">
      <h4><label>@lang( 'role.dashboard' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'dashboard.data', in_array('dashboard.data', $role_permissions),
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
          {!! Form::checkbox('permissions[]', 'payday', in_array('payday', $role_permissions),
            [ 'class' => 'input-icheck']); !!} PayRoll
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'account.account' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'account.access', in_array('account.access', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.edit', in_array('account.edit', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_accounts' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.link_account', in_array('account.link_account',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.link_account' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'account.reconcile', in_array('account.reconcile',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.reconcile' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'contact.customer_statement' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'enable_separate_customer_statement_no',
            in_array('enable_separate_customer_statement_no', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'contact.enable_separate_customer_statement_no' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_customer_statement', in_array('edit_customer_statement',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'contact.edit_customer_statement' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'lang_v1.customer_reference' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'customer_reference.edit',
            in_array('customer_reference.edit', $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'mpcs.access', in_array('mpcs.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f9c_form', in_array('f9c_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f9c_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f15a9abc_form', in_array('f15a9abc_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f15a9abc_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f16a_form', in_array('f16a_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f16a_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f21c_form', in_array('f21c_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21c_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f17_form', in_array('f17_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f17_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f14b_form', in_array('f14b_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f14b_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f20_form', in_array('f20_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f20_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f21_form', in_array('f21_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'f22_stock_taking_form', in_array('f22_stock_taking_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.f22_stock_taking_form' ) }}
          </label>
        </div>
      </div>

      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_f22_stock_Taking_form', in_array('edit_f22_stock_Taking_form',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f22_stock_Taking_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_f17_form', in_array('edit_f17_form', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f17_form' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'mpcs_form_settings', in_array('mpcs_form_settings',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs_form_settings' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'list_opening_values', in_array('list_opening_values',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'ran.access', in_array('ran.access',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'catalogue.access', in_array('catalogue.access',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'repair.access', in_array('repair.access',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'petro.access', in_array('petro.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.petro.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'fuel_tank.edit', in_array('fuel_tank.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.fuel_tank.edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'meter_resetting_tab', in_array('meter_resetting_tab',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.meter_resetting_tab' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'add_dip_resetting', in_array('add_dip_resetting',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_dip_resetting' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'edit_other_income_prices', in_array('edit_other_income_prices',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_other_income_prices' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'daily_collection.delete', in_array('daily_collection.delete',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_collection.delete' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'petro::lang.settlement' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'settlement.edit', in_array('settlement.edit', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_settlement' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'reset_dip', in_array('reset_dip', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.reset_dip' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'petro::lang.pump_operator' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'pum_operator.active_inactive',
            in_array('pum_operator.active_inactive', $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.pum_operator.active_inactive' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'pump_operator.dashboard', in_array('pump_operator.dashboard',
            $role_permissions),
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
      <h4><label>@lang( 'role.issue_customer_bill' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.access', in_array('issue_customer_bill.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.add', in_array('issue_customer_bill.add',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.add' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'issue_customer_bill.view', in_array('issue_customer_bill.view',
            $role_permissions),
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
      <h4><label>@lang( 'role.customer_settings' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'customer_settings.access', in_array('customer_settings.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer_settings.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'approve_sell_over_limit', in_array('approve_sell_over_limit',
            $role_permissions),
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
      <h4><label>@lang( 'role.tasks_management' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'tasks_management.access', in_array('tasks_management.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tasks_management.tasks', in_array('tasks_management.tasks',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.tasks' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'tasks_management.reminder', in_array('tasks_management.reminder',
            $role_permissions),
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
      <h4><label>@lang( 'role.member_registration')</label></h4>
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
            {!! Form::checkbox('permissions[]', 'member_registration.access', in_array('member_registration.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.member_registration.access' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'add_remarks', in_array('add_remarks',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_remarks' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'update_status_of_issue', in_array('update_status_of_issue',
            $role_permissions),
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
      <h4><label>@lang( 'role.leads')</label></h4>
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
            {!! Form::checkbox('permissions[]', 'leads.view', in_array('leads.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.view' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.create', in_array('leads.create',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.create' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.edit', in_array('leads.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.edit' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.delete', in_array('leads.delete',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.delete' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.import', in_array('leads.import',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.import' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leads.settings', in_array('leads.settings',
            $role_permissions),
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
            {!! Form::checkbox('permissions[]', 'day_count', in_array('day_count',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.day_count') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['sms_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.sms')</label></h4>
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
            {!! Form::checkbox('permissions[]', 'sms.access', in_array('sms.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.access' ) }}
          </label>
        </div>
      </div>

      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sms.list', in_array('sms.list',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.list' ) }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <hr>
  @endif



  @if($get_permissions['enable_cheque_writing'])
  <div class="row check_group">
    <div class="col-md-3">
      <h4><label>@lang( 'lang_v1.enable_cheque_writing' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'enable_cheque_writing', in_array('enable_cheque_writing',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_cheque_writing' ) }}
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
      <h4><label>@lang( 'restaurant.bookings' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'crud_all_bookings', in_array('crud_all_bookings',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'crud_own_bookings', in_array('crud_own_bookings',
            $role_permissions),
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
      <h4><label>@lang( 'lang_v1.access_selling_price_groups' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'access_default_selling_price',
            in_array('access_default_selling_price',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
          </label>
        </div>
      </div>
      @if(count($selling_price_groups) > 0)
      @foreach($selling_price_groups as $selling_price_group)
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id,
            in_array('selling_price_group.' . $selling_price_group->id, $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
          </label>
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>


  @if(auth()->user()->can('product.set_min_sell_price'))
  <div class="row">
    <div class="col-md-3">
      <h4><label>@lang( 'lang_v1.min_sell_price' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('product.set_min_sell_price')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'product.set_min_sell_price', in_array('product.set_min_sell_price',
            $role_permissions),
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
      <h4><label>@lang( 'lang_v1.sales_commission_agents_create' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('sales-commission-agents.create')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sales-commission-agents.create',
            in_array('sales-commission-agents.create',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.sales_commission_agents_create') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <hr>
  @endif

  @if($get_permissions['hr_module'])
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'lang_v1.hr' )</label></h4>
    </div>
    <div class="col-md-2">
      <div class="checkbox">

        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

      </div>
    </div>
    <div class="col-md-9">
      @can('hr.access')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.access', in_array('hr.access',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.access') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.employee', in_array('hr.employee',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.employee') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.attendance', in_array('hr.attendance',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.attendance') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.payroll', in_array('hr.payroll',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.payroll') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.reports', in_array('hr.reports',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.reports') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.notice_board', in_array('hr.notice_board',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.notice_board') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'hr.settings', in_array('hr.settings',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('hr::lang.hr.settings') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.employee' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'employee.edit', in_array('employee.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.employee.edit') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.attendance' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'attendance.approve_reject_lo',
            in_array('attendance.approve_reject_lo',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.attendance.approve_reject_lo') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'role.leave_reqeust' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'leave_request.approve_reject',
            in_array('leave_request.approve_reject',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.approve_reject') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leave_request.delete', in_array('leave_request.delete',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.delete') }}
          </label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'leave_request.edit', in_array('leave_request.edit',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('role.leave_request.edit') }}
          </label>
        </div>
      </div>
    </div>
  </div>
  @endif
  <hr>
  @if(auth()->user()->can('day_end.view') || auth()->user()->can('day_end.bypass'))
  <div class="row check_group">
    <div class="col-md-1">
      <h4><label>@lang( 'lang_v1.day_end' )</label></h4>
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
            {!! Form::checkbox('permissions[]', 'day_end.view', in_array('day_end.view',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_view') }}
          </label>
        </div>
      </div>
      @endcan
      @can('day_end.bypass')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'day_end.bypass', in_array('day_end.bypass',
            $role_permissions),
            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_bypass') }}
          </label>
        </div>
      </div>
      @endcan
    </div>
  </div>
  @endif

  @if($get_permissions['upload_images'])
  @if(auth()->user()->can('upload_images'))
  <div class="row">
    <div class="col-md-3">
      <h4><label>@lang( 'lang_v1.upload_images' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('upload_images')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'upload_images', in_array('upload_images',
            $role_permissions),
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
      <h4><label>@lang( 'lang_v1.sms' )</label></h4>
    </div>
    <div class="col-md-9">
      @can('sms.view')
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'sms.view', in_array('sms.view',
            $role_permissions),
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
      <h4><label>@lang( 'lang_v1.restaurant' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'restaurant.access', in_array('restaurant.access',
            $role_permissions),
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
      <h4><label>@lang( 'lang_v1.clear_cache' )</label></h4>
    </div>
    <div class="col-md-9">
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', 'cache_clear', in_array('cache_clear',
            $role_permissions),
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
      <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
    </div>
  </div>

  {!! Form::close() !!}
  @endcomponent
</section>
<!-- /.content -->
@endsection