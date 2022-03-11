@extends('layouts.app')

@section('title', __('lang_v1.'.$type.'s'))

@php

      $contact_fields = !empty(session('business.contact_fields')) ? session('business.contact_fields') : [];

@endphp

@section('content')



<!-- Content Header (Page header) -->
<style>
  
.popup{
   
    cursor: pointer
}
.popupshow{
    z-index: 99999;
    display: none;
}
.popupshow .overlay{
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.66);
    position: absolute;
    top: 0;
    left: 0;
}
.popupshow .img-show{
        width: 900px;
    height: 600px;
    background: #FFF;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    overflow: hidden;
}
.img-show span{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
    cursor: pointer;
}
.img-show img{
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
/*End style*/

</style>
<section class="content-header">

    <h1> @lang('lang_v1.'.$type.'s')

        <small>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.'.$type.'s') ])</small>

    </h1>

    <!-- <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>

        <li class="active">Here</li>

    </ol> -->

</section>



<!-- Main content -->

<section class="content">

    @php 

        if($type == 'customer'){

            $colspan = 15;



        }else{

            $colspan = 16;

        }



    @endphp

    <input type="hidden" value="{{$type}}" id="contact_type">

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.all_your_contact', ['contacts' =>

    __('lang_v1.'.$type.'s') ])])

    @if(auth()->user()->can('supplier.create') || auth()->user()->can('property.customer.create'))

    @slot('tool')

    <div class="box-tools">

        <button type="button" class="btn btn-block btn-primary btn-modal"

            data-href="{{action('\Modules\Property\Http\Controllers\ContactController@create', ['type' => $type])}}" data-container=".contact_modal">

            <i class="fa fa-plus"></i> @lang('messages.add')</button>

    </div>

    @endslot

    @endif

    @if(auth()->user()->can('supplier.view') || auth()->user()->can('property.customer.view'))

    <div class="table-responsive">

        <table class="table table-bordered table-striped" id="property_contact_table">

            <thead>

                <tr>

                    <td colspan="{{$colspan}}">

                        <div style="display: flex; width: 100%;">

                            @if(auth()->user()->can('property.customer.delete') || auth()->user()->can('supplier.delete'))

                            {!! Form::open(['url' => action('ContactController@massDestroy'), 'method' => 'post', 'id'

                            => 'mass_delete_form' ]) !!}

                            {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}

                            {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger',

                            'id' => 'delete-selected')) !!}

                            {!! Form::close() !!}

                            @endif

                        </div>

                    </td>

                </tr>

                <tr>

                    <th><input type="checkbox" id="select-all-row"></th>

                    <th class="notexport">@lang('messages.action')</th>

                    <th>@lang('lang_v1.contact_id')</th>

                    @if($type == 'supplier')

                    <th>@lang('business.business_name')</th>

                    <th>@lang('contact.name')</th>

                    <th>NIC Number</th>

                    <th>@lang('contact.mobile')</th>

                    <th>@lang('lang_v1.supplier_group')</th>

                    <th>@lang('contact.pay_term')</th>

                    <th>@lang('contact.total_purchase_due')</th>

                    <th>@lang('lang_v1.total_purchase_return_due')</th>

                    <th>@lang('account.opening_balance')</th>

                    <th>@lang('business.email')</th>

                    <th>@lang('contact.tax_no')</th>

                    <th>@lang('lang_v1.added_on')</th>

                    @elseif( $type == 'customer')

                    <th>@lang('user.name')</th>

                    <th>NIC Number</th>

                    <th>@lang('contact.mobile')</th>

                    <th>@lang('lang_v1.customer_group')</th>

                    <th>@lang('property::lang.total_amount_due')</th>

                    <th>@lang('lang_v1.added_on')</th>

                    @if($reward_enabled)

                    <th id="rp_col">{{session('business.rp_name')}}</th>

                    @endif

                    @endif

                    @if($type=='customer' && !array_key_exists('property_customer_custom_field_1', $contact_fields))

                    <th>

                       

                        @lang('Photo')

                    </th>

                    @endif

                    @if($type=='customer' && !array_key_exists('property_customer_custom_field_2', $contact_fields))

                    <th>

                        @lang('lang_v1.signature')

                  

                    </th>

                    @endif

                    

                  

                    

                </tr>

            </thead>

            <tfoot>

                <tr class="bg-gray font-17 text-center footer-total">

                    <td @if($type=='supplier' ) colspan="6" @elseif( $type=='customer' ) colspan="6"  @endif>

                        <strong>

                            @lang('sale.total'):

                        </strong>

                    </td>

                    <td><span class="display_currency" id="footer_contact_due" data-currency_symbol="true"></span></td>

                    <td></td>

                 

                

                  

                </tr>

            </tfoot>

        </table>

    </div>

    @endif

    @endcomponent



    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>



</section>
<div class="popupshow">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div>
<!-- /.content -->



@endsection



@section('javascript')

<script>

    //page as expended mode

    var body = document.getElementsByTagName("body")[0];

    body.className += " sidebar-collapse";



    $(document).on('click', '#delete-selected', function(e){

        e.preventDefault();

        var selected_rows = getSelectedRows();



        if(selected_rows.length > 0){

        $('input#selected_rows').val(selected_rows);

        swal({

            title: LANG.sure,

            icon: "warning",

            buttons: true,

            dangerMode: true,

        }).then((willDelete) => {

            if (willDelete) {

            $('form#mass_delete_form').submit();

            }

        });

        } else{

        $('input#selected_rows').val('');

        swal('@lang("lang_v1.no_row_selected")');

        }    

    });

    function getSelectedRows() {

        var selected_rows = [];

        var i = 0;

        $('.row-select:checked').each(function () {

            selected_rows[i++] = $(this).val();

        });



        return selected_rows; 

    }



    $(document).ready(function(){

     



        var property_contact_table_type = $('#contact_type').val();

        if (property_contact_table_type == 'supplier') {

            var columns = [

                { data: 'mass_delete', searchable: false, orderable: false },

                { data: 'action', searchable: false, orderable: false },

                { data: 'contact_id', name: 'contact_id' },

                { data: 'supplier_business_name', name: 'supplier_business_name' },

                { data: 'name', name: 'name' },

                { data: 'nic_number', name: 'nic_number' },

                { data: 'mobile', name: 'mobile' },

                { data: 'supplier_group', name: 'cg.name' },

                { data: 'pay_term', name: 'pay_term', searchable: false, orderable: false },

                { data: 'due', searchable: false, orderable: false },

                { data: 'return_due', searchable: false, orderable: false },

                { data: 'opening_balance', name: 'opening_balance', searchable: false },

                { data: 'email', name: 'email' },

                { data: 'tax_number', name: 'tax_number' },

                { data: 'created_at', name: 'contacts.created_at' },

                { data: 'custom_field1', name: 'custom_field1', searchable: false, orderable: false },

                { data: 'custom_field2', name: 'custom_field2', searchable: false, orderable: false },

                

              

                @if(isset($contact->image) && $contact->image!=null )

                {   "render": function (data, type, row, meta) {
                    $path = asset('/uploads/media/'.$contact->image);
                     return  '<img src="{{asset($path)}} " width="50" height="502">';

                     },

                     @endif

                     @if(isset($contact->signature) && $contact->signature!=null )

                 {   "render": function (data, type, row, meta) {
                    $pathSingature = asset('/uploads/media/'.$contact->signature);
                      return '<img src="{{$pathSingature}}" width="50" height="50">';

                       },

                      @endif

             

            ];

        } else if (property_contact_table_type == 'customer') {

            var columns = [

                { data: 'mass_delete', searchable: false, orderable: false },

                { data: 'action', searchable: false, orderable: false },

                { data: 'contact_id', name: 'contact_id' },

                { data: 'name', name: 'name' },

                { data: 'nic_number', name: 'nic_number' },

                { data: 'mobile', name: 'mobile' },

                { data: 'customer_group', name: 'cg.name' },

                { data: 'due', searchable: false, orderable: false },

                { data: 'created_at', name: 'contacts.created_at' },
                 { data: 'image', name: 'contacts.image' },
                  { data: 'signature', name: 'contacts.signature' },
                

                
 
                

                //    @if(isset($contact->image) && $contact->image!=null )

                  

                //     //<img src="{{asset('/uploads/media/'.$contact->image)}} " width="50" height="50">

                   

                    

                    

                    

                // @if($type=='customer' && !array_key_exists('property_customer_custom_field_1', $contact_fields))

         

                //      { data: 'image', name: 'contacts.image' },

                //     @endif

                //    @endif

                 

                // @if($type=='customer' && !array_key_exists('property_customer_custom_field_2', $contact_fields))

  

                //       { data: 'signature', name: 'contacts.signature' },

                 

               

                

                

                

                 

                //  @endif

       

                    

                   

                  

             

            ];

            if ($('#rp_col').length) {

                columns.push({ data: 'total_rp', name: 'total_rp' });

            }

        }

        var property_contact_table = $('#property_contact_table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: '/property/contacts',

                data: function (d) {

                    d.type = $('#contact_type').val();

                },

            },

            aaSorting: [[1, 'desc']],

            columns: columns,

            @include('layouts.partials.datatable_export_button')

            fnDrawCallback: function (oSettings) {

                var total_due = sum_table_col($('#property_contact_table'), 'contact_due');

                $('#footer_contact_due').text(total_due);

                var total_return_due = sum_table_col($('#property_contact_table'), 'return_due');

                $('#footer_contact_return_due').text(total_return_due);

                var total_opening_balance = sum_table_col($('#property_contact_table'), 'ob');

                $('#footer_contact_opening_balance').text(total_opening_balance);

                __currency_convert_recursively($('#property_contact_table'));

            },

        });



        $(document).on('click', '.delete_contact_button', function (e) {

            e.preventDefault();

            swal({

                title: LANG.sure,

                text: LANG.confirm_delete_contact,

                icon: 'warning',

                buttons: true,

                dangerMode: true,

            }).then((willDelete) => {

                if (willDelete) {

                    var href = $(this).attr('href');

                    var data = $(this).serialize();

                    $.ajax({

                        method: 'DELETE',

                        url: href,

                        dataType: 'json',

                        data: data,

                        success: function (result) {

                            if (result.success == true) {

                                toastr.success(result.msg);

                                property_contact_table.ajax.reload();

                            } else {

                                toastr.error(result.msg);

                            }

                        },

                    });

                }

            });

        });



    })

   $(document).ready(function(){

    
    $('body').on('click', '.popup', function () { 
        var $src = $(this).attr("src");
        $(".popupshow").fadeIn();
        $(".img-show img").attr("src", $src);
    });
    
   
        $('body').on('click', '.overlay', function () {

        $(".popupshow").fadeOut();
    });
    $('body').on('click', 'span', function () {
        $(".popupshow").fadeOut();
    });
});
</script>

@endsection