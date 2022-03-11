@extends('layouts.ecom_customer')
@section('title', __('customer.add_order'))

@section('content')

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['action' => 'Ecom\EcomCustomerOrderController@create', 'method' => 'GET', 'id'
            =>'filter_form']) !!}
            <div class="col-md-3 @if(Auth::user()->is_company_customer) hide @endif">
                <div class="form-group">
                    {!! Form::label('business_category', __('customer.business_category') . ':') !!}
                    {!! Form::select('business_category', $business_categories, null, ['id' => 'business_category',
                    'class' => 'form-control select2', 'style' => 'width:100%' , 'placeholder' => 'Please Select']); !!}
                </div>
            </div>
            <div class="col-md-3 @if(Auth::user()->is_company_customer) hide @endif">
                <div class="form-group">
                    {!! Form::label('country', __('customer.country') . ':') !!}
                    {!! Form::select('country', $countries, null, ['id' => 'country',
                    'class' => 'form-control select2', 'style' => 'width:100%' , 'placeholder' => 'Please Select']); !!}
                </div>
            </div>
            <div class="col-md-3 @if(Auth::user()->is_company_customer) hide @endif">
                <div class="form-group">
                    {!! Form::label('city', __('customer.city') . ':') !!}
                    {!! Form::select('city', [], null, ['id' => 'select_city', 'class' => 'form-control
                    select2', 'required', 'style' => 'width:100%', 'placeholder' => 'Please Select']); !!}
                </div>
            </div>
            @if(!Auth::user()->is_company_customer)
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('business_id', __('customer.business') . ':') !!}
                    {!! Form::select('business_id', [], null, ['id' => 'select_business_id', 'class' => 'form-control
                    select2', 'required', 'style' => 'width:100%', 'placeholder' => 'Please Select']); !!}
                </div>
            </div>
            @else
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('customer.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['id' => 'select_location_id', 'class' => 'form-control
                        select2', 'required', 'style' => 'width:100%', 'placeholder' => 'Please Select']); !!}
                    </div>
                </div>
            <input type="hidden" name="business_id" value="{{Auth::user()->business_id}}">
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_mode', __('customer.order_mode') . ':') !!}
                    {!! Form::select('order_mode', ['by_upload_document_image' =>
                    __('customer.by_upload_document_image'), 'select_from_store' => __('customer.select_from_store')],
                    null, ['id' => 'order_mode', 'class' => 'form-control
                    select2', 'required', 'style' => 'width:100%', 'placeholder' => 'Please Select']); !!}
                </div>
            </div>
            <button class="btn btn-success" style="margin-top: 24px;" type="button"
                id="submit_btn">@lang('customer.select')</button>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
    @if(!Auth::user()->is_company_customer)
    $('#country').change(function(){
        $('#select_city').empty().append(`<option value="">Please Select</option>`);
        $('#select_business_id').empty().append(`<option value="">Please Select</option>`);
        getdata();
    });
    $('#select_city').change(function(){
        getdata();
    });
    @endif
    function getdata(){
        $.ajax({
            method: 'get',
            url: "{{action('BusinessController@getBusinessByCategory')}}",
            data: { category : $('#business_category').val() , country : $('#country').val(), city :  $('#select_city').val()},
            dataType : "json",
            success: function(result) {
                console.log(result.type);
                
                if(result.type === 'cities'){
                    $('#select_city').empty().append(result.html);
                }
                if(result.type === 'businesses'){
                    $('#select_business_id').empty().append(result.html);
                }
            },
        });
        
    }
    $('#submit_btn').click(function(e){
        e.preventDefault();
        @if(!Auth::user()->is_company_customer)
        if($('#select_business_id').find(':selected').val() == ''){
           toastr.error('Please select business');
        }
        @else
        if($('#select_location_id').find(':selected').val() == ''){
           toastr.error('Please select business location');
        }
        @endif 
        if($('#order_mode').find(':selected').val() == ''){
            toastr.error('Please select order mode');
        }else{
            $('#filter_form').submit();
        }
    })
</script>
@endsection