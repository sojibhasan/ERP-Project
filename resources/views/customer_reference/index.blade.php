@extends('layouts.app')
@section('title', __('lang_v1.customer_reference'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.customer_reference')
        <small>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.customer_reference') ])</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_your_contact_reference')])
    @if(auth()->user()->can('crm.create'))
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('CustomerReferenceController@create')}}"
            data-container=".customer_reference_modal" id="add_customer_reference">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    @endif
    @if(auth()->user()->can('crm.view') )
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="customer_reference_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('lang_v1.customer')</th>
                    <th>@lang('lang_v1.reference')</th>
                    <th>@lang('lang_v1.barcode')</th>
                    <th>@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endif
    @endcomponent

    <div class="modal fade customer_reference_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    var columns = [
            { data: 'date', name: 'date' },
            { data: 'contact_name', name: 'contact_name' },
            { data: 'reference', name: 'reference' },
            { data: 'barcode_src', name: 'barcode_src' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    
    var customer_reference_table = $('#customer_reference_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{action("CustomerReferenceController@index")}}',
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        customer_reference_table.ajax.reload();
                    },
                });
            }
        });
    });

$('.add_reference_btn').click(function (e) {
    e.preventDefault();
    url = "{{action('CustomerReferenceController@store')}}";
    data = $('#customer_reference_add_form').serialize();

    $.ajax({
        method: 'post',
        url: url,
        data:  data,
        success: function(result) {
            console.log(result);
            if(result.success == 1){
                toastr.success(result.msg);
            }else{
                toastr.error(result.msg);
            }
            
        },
    });
});

$(document).on('click', 'a.barcode_print', function(e) {
    let id = $(this).data('id');
    let src = $('.barcode_show'+id).attr('src');
    
    var w = window.open('', '_self');
    var html = '<img src="'+src+'">';
    $(w.document.body).html(html);
    w.print();
    w.close();
    location.reload();
    

});

$('#add_customer_reference').click(function(){
  		$('.customer_reference_modal').modal({
    		backdrop: 'static',
    		keyboard: false
		});
	});
</script>
@endsection