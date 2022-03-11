<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CustomerReferenceController@update', $customer_reference->id), 'method' => 'put', 'id' =>
        'customer_reference_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.edit_customer_reference' )</h4>
        </div>

        <div class="modal-body">
            <div class="colr-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'lang_v1.date' ) . ':*') !!}
                    {!! Form::text('date', null, ['class' => 'form-control reference_date', 'required',
                    'placeholder' => __(
                    'lang_v1.date' ) ]); !!}
                </div>
            </div>
            <div class="colr-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'lang_v1.customer' ) . ':*') !!}
                    {!! Form::select('contact_id', $contacts, $customer_reference->contact_id , ['class' => 'form-control select2
                    contact_reference',
                    'placeholder' => __(
                    'lang_v1.please_select' ), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
            <div class="colr-md-4">
                <div class="form-group">
                    {!! Form::label('reference', __( 'lang_v1.reference' ) . ':*') !!}  @if(!empty($help_explanations['customer_reference'])) @show_tooltip($help_explanations['customer_reference']) @endif
                    {!! Form::text('reference', $customer_reference->reference, ['class' => 'form-control', 'placeholder' => __(
                    'lang_v1.reference' ) ]); !!}
                </div>
                <input type="hidden" name="barcode_src" value="{{$customer_reference->barcode_src}}" id="barcode_src">
            </div>
            <div class="col-md-12">
                {!! Form::label('barcode', __( 'lang_v1.barcode' ) ) !!}
                <div class="col-md-12 barcode-image">
                    <img style="max-width: 97%;" src="{{$customer_reference->barcode_src}}" alt="barcode">
                </div>
            </div>

            <div class="clearfix"></div>
          
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default add_reference_btn" data-dismiss="modal">@lang(
                    'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.reference_date').datepicker('setDate', "{{\Carbon::parse($customer_reference->date)->format('m-d-Y')}}");

        $('#reference').change(function(){
            $.ajax({
                method: 'get',
                url: '/get-customer-reference/barcode',
                data: { 
                    customer_id : $('#contact_id').val(),
                    reference : $('#reference').val()
                 },
                success: function(result) {
                    if(result.success == 1){
                        $('.barcode-image').empty().append(result.html);
                        $('#barcode_src').val(result.src);
                    }
                    
                },
            });
        })
    </script>