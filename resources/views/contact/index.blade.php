@extends('layouts.app')
@section('title', __('lang_v1.'.$type.'s'))

@section('content')

<!-- Content Header (Page header) -->
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
            $colspan = 17;
        }

    @endphp
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.all_your_contact', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal"
            data-href="{{action('ContactController@create', ['type' => $type])}}" data-container=".contact_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    @endif
    @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view'))
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="contact_table">
            <thead>
                <tr>
                    <td colspan="{{$colspan}}">
                        <div style="display: flex; width: 100%;    justify-content: space-between;">
                            @if(auth()->user()->can('customer.delete') || auth()->user()->can('supplier.delete'))
                            {!! Form::open(['url' => action('ContactController@massDestroy'), 'method' => 'post', 'id'
                            => 'mass_delete_form' ]) !!}
                            {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                            {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger',
                            'id' => 'delete-selected')) !!}
                            {!! Form::close() !!}

                            @endif

                            @if($type == 'customer')
                              <div id='total_os' class="text-danger"><div>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><input type="checkbox" id="select-all-row"></th>
                    <th class="notexport">@lang('messages.action')</th>
                    <th width="68">@lang('lang_v1.contact_id')</th>
                    @if($type == 'supplier')
                    <th>@lang('business.business_name')</th>
                    <th>@lang('contact.name')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('lang_v1.supplier_group')</th>
                    <th>@lang('contact.pay_term')</th>
                    <th>@lang('contact.total_purchase_due')</th>
                    <th>@lang('lang_v1.total_purchase_return_due')</th>
                    <th html="true">@lang('contact.opening_bal_due')</th>
                    <th>@lang('account.opening_balance')</th>
                    <th>@lang('business.email')</th>
                    <th>@lang('contact.tax_no')</th>
                    <th>@lang('lang_v1.added_on')</th>
                    @elseif( $type == 'customer')
                        <th>@lang('user.name')</th>
                        <th>@lang('contact.mobile')</th>
                        <th width="111">@lang('lang_v1.customer_group')</th>
                        <th width="82">@lang('lang_v1.credit_limit')</th>
                        <th width="142" style="color: #9D0606">@lang('contact.total_due')</th>
                        <!-- <th width="150" style="min-width: 100px"> @lang('contact.total_sale_due')</th> -->
                        <th width="150" style="min-width: 100px"> @lang('lang_v1.total_sell_return_due') </th>
                        <th width="65">@lang('contact.pay_term')</th>
                        <!-- <th width="125">@lang('account.opening_balance')</th> -->
                    
                        <!--
                        <th>@lang('contact.tax_no')</th>
                        <th>@lang('business.email')</th>
                        <th>@lang('business.address')</th>
                        -->
                        <th width="70">@lang('lang_v1.added_on')</th>
                    @if($reward_enabled)
                    <th id="rp_col">{{session('business.rp_name')}}</th>
                    @endif
                    @endif
                    <th class="contact_custom_field1 @if($is_property && !array_key_exists('property_customer_custom_field_1', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_1', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_1', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field1')
                    </th>
                   
                    <th class="contact_custom_field2 @if($is_property && !array_key_exists('property_customer_custom_field_2', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_2', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_2', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field2')
                    </th>
                    
                    <th class="contact_custom_field3 @if($is_property && !array_key_exists('property_customer_custom_field_3', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_3', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_3', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field3')
                    </th>
                    
                    <th class="contact_custom_field4 @if($is_property && !array_key_exists('property_customer_custom_field_4', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_4', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_4', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field4')
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td @if($type=='supplier' ) colspan="6" @elseif( $type=='customer' ) @if($reward_enabled)
                        colspan="7" @else colspan="7" @endif @endif>
                        <strong>
                            @lang('sale.total'):
                        </strong>
                    </td>
                    <td><span class="display_currency" id="footer_contact_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_tot_due" data-currency_symbol="true"></span></td>
                    @if($type == 'supplier')
                    <td><span class="display_currency" id="footer_contact_return_due"
                            data-currency_symbol="true"></span></td>
                        <td></td> 
                  
                    <td><span class="" id="footer_contact_due_opening_balances"></span></td>
                   
                    
                    
                    <td><span class="display_currency" id="footer_contact_opening_balance"
                            data-currency_symbol="true"></span></td>
                     @endif
                            <td></td> 
                            @if($type == 'customer')
                            <td></td> 

                     @endif


                    
                    
                   
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
<!-- /.content -->

@endsection

@section('javascript')
<script>
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
    $.ajax({
        method: 'get',
        url: 'contacts/get_outstanding',

        success: function(result) {
            if (result) {
               $('#total_os').html(result);               
            }
        },
    });
    
</script>
@endsection