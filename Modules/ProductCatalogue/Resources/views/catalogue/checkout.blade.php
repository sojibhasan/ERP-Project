@extends('layouts.guest')
@section('title', $business->name)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header text-center">
    <h2>{{$business->name}}</h2>
    <h4 class="mb-0">{{$business_location->name}}</h4>
    <p>{!! $business_location->location_address !!}</p>
</section>
<!--Section: Block Content-->
<section>
    {!! Form::open(['url' => action('\Modules\ProductCatalogue\Http\Controllers\CartController@checkout'), 'method' => 'post', 'id' => 'cart_form']) !!}
    <div class="container">
        <!--Grid row-->
        <div class="row">
            <!--Grid column-->
            <div class="col-lg-8 mb-4">

                <!-- Card -->
                <div class="card wish-list pb-1">
                    <div class="card-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', __('contact.name') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
                                    __('contact.name'), 'required']);
                                    !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::hidden('contact_id', $contact_id, ['class' => 'form-control','placeholder' =>
                        __('lang_v1.contact_id'), 'readonly']); !!}

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('email', __('business.email') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    {!! Form::email('email', null, ['class' => 'form-control','placeholder' =>
                                    __('business.email')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('password', __('business.password') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-key"></i>
                                    </span>

                                    {!! Form::password('password', ['class' => 'form-control', 'id' =>
                                    'password','placeholder' =>
                                    __('business.password')]); !!}
                                </div>
                                <p class="help-block">At least 6 character.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-key"></i>,
                                    </span>
                                    {!! Form::password('confirm_password', ['class' => 'form-control', 'id' =>
                                    'confirm_password',
                                    'placeholder' => __('business.confirm_password')]); !!}
                                </div>
                                <p class="help-block">At least 6 character.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </span>
                                    {!! Form::text('mobile', null, ['class' => 'form-control input_number', 'required',
                                    'placeholder' =>
                                    __('contact.mobile')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    {!! Form::text('alternate_number', null, ['class' => 'form-control input_number',
                                    'placeholder' =>
                                    __('contact.alternate_contact_number')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('landline', __('contact.landline') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    {!! Form::text('landline', null, ['class' => 'form-control input_number',
                                    'placeholder' => __('contact.landline')]);
                                    !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('city', __('business.address') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' =>
                                    __('business.address')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('city', __('business.city') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' =>
                                    __('business.city')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('state', __('business.state') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' =>
                                    __('business.state')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('country', __('business.country') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-globe"></i>
                                    </span>
                                    {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' =>
                                    __('business.country')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('landmark', __('business.landmark') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::text('landmark', null, ['class' => 'form-control',
                                    'placeholder' => __('business.landmark')]); !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Card -->

            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-4">

                <!-- Card -->
                <div class="card mb-4">
                    <div class="card-body">

                        <ul class="list-group list-group-flush">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Total amount
                                <span>{{$business->currency->symbol}} {{@num_format($total)}}</span>
                            </li>
                           
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div>
                                    <strong>The total amount of</strong>
                                    <strong>
                                        <p class="mb-0">(including VAT)</p>
                                    </strong>
                                </div>
                                <span><strong>{{$business->currency->symbol}} {{@num_format($total)}}</strong></span>
                            </li>
                        </ul>

                        <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">Make
                            payment</button>

                    </div>
                </div>
                <!-- Card -->



            </div>
            <!--Grid column-->

        </div>
        <!--Grid row-->
    </div>
    {!! Form::close() !!}
</section>
<!--Section: Block Content-->
<!--Section: Block Content-->



@endsection

@section('javascript')
<script>
    $('.qty').change(function(){
        let price = parseFloat($(this).parent().parent().find('.price').data('price'));
        let qty = parseFloat($(this).val());

        let sub_total = price * qty;
        $(this).parent().parent().find('.sub_total').text(__number_f(sub_total, false, false, __currency_precision));
        $(this).parent().parent().find('.sub_total').data( 'sub_total' ,sub_total);

        calculate_cart_total();
    })

    function calculate_cart_total(){
        grand_total = 0;
        $('#cart').find('.sub_total').each((i, ele) => {
            grand_total += parseFloat($(ele).data('sub_total'));
        })

        $('#cart').find('.grand_total').text(__number_f(grand_total, false, false, __currency_precision))
    }
</script>
@endsection