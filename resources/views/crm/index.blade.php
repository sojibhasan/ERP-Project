@extends('layouts.app')
@section('title', __('lang_v1.'.$type))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.'.$type)
        <small>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.'.$type) ])</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.all_your_contact', ['contacts' =>
    __('lang_v1.'.$type) ])])
    @if(auth()->user()->can('crm.create'))
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('CRMController@create')}}"
            data-container=".crm_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    @endif
    @if(auth()->user()->can('crm.view') )
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="crm_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.contact_id')</th>
                    <th>@lang('lang_v1.added_on')</th>
                    <th>@lang('lang_v1.business_name')</th>
                    {{-- <th>@lang('lang_v1.crm_group')</th> --}}
                    <th>@lang('business.address')</th>
                    <th>@lang('lang_v1.town')</th>
                    <th>@lang('lang_v1.district')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('lang_v1.user')</th>
                    <th>@lang('messages.action')</th>

                </tr>
            </thead>
            <tfoot>
          
            </tfoot>
        </table>
    </div>
    @endif
    @endcomponent

    <div class="modal fade crm_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade crm_show" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade crm_edit" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    var columns = [
            { data: 'contact_id', name: 'contact_id' },
            { data: 'created_at', name: 'created_at' },
            { data: 'business_name', name: 'business_name' },
            { data: 'address', name: 'address' },
            { data: 'town', name: 'town' },
            { data: 'district', name: 'district' },
            { data: 'mobile', name: 'mobile' },
            { data: 'user', name: 'user' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    
    var crm_table = $('#crm_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{action("CRMController@index")}}',
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_crm_button', function(e) {
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
                        crm_table.ajax.reload();
                    },
                });
            }
        });
    });

</script>
@endsection