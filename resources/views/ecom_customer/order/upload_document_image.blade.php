@extends('layouts.ecom_customer')
@section('title', __('customer.add_order'))

@section('content')

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['title' => __('customer.upload_document_image'), 'class' => 'box-primary'])
            {!! Form::open(['action' => 'Ecom\EcomCustomerOrderController@uploadDocumentImageSave', 'method' => 'POST', 'id'
            =>'filter_form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}
            {!! Form::hidden('business_id', $business_id, []) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('upload_document_image', __('customer.upload_document_image') . ':') !!}
                   {!! Form::file('upload_document_image', ['class' => '' , 'id' => 'upload_document_image', 'style' => 'margin-top: 8px;', 'required']) !!}
                </div>
            </div>
            <button class="btn btn-success" style="margin-top: 24px;" type="submit"
                id="submit_btn">@lang('customer.upload')</button>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
    $('#business_category').change(function(){
      $.ajax({
          method: 'get',
          url: "{{action('BusinessController@getBusinessByCategory')}}",
          data: { category : $(this).val() },
          dataType : "html",
          success: function(result) {
             $('#select_business_id').empty().append(result);
              
          },
      });
        
    });

    $('#submit_btn').click(function(e){
        e.preventDefault();
        if($('#select_business_id').find(':selected').val() == ''){
           toastr.error('Please select business');
        }else if($('#order_mode').find(':selected').val() == ''){
            toastr.error('Please select order mode');
        }else{
            $('#filter_form').submit();
        }
    })
</script>
@endsection