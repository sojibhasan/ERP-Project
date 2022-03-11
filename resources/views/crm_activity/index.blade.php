@extends('layouts.app')
@section('title', __('lang_v1.crm_activity'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.crm_activity')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_your_crm_activity') ])

    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('CRMActivityController@create')}}"
            data-container=".crm_modal">
            <i class="fa fa-plus"></i> @lang('lang_v1.add_crm_activity')</button>
    </div>
    @endslot
    
    @if(auth()->user()->can('crm.view') )
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="crm_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('lang_v1.name')</th>
                    <th>@lang('lang_v1.email')</th>
                    <th>@lang('business.mobile')</th>
                    <th>@lang('lang_v1.alternate_number')</th>
                    <th>@lang('contact.landline')</th>
                    <th>@lang('contact.city')</th>
                    <th>@lang('lang_v1.district')</th>
                    <th>@lang('lang_v1.country')</th>
                    <th>@lang('lang_v1.time_connected')</th>
                    <th>@lang('lang_v1.note')</th>
                    <th>@lang('lang_v1.next_follow_up_date')</th>
                    <th>@lang('lang_v1.discontinue_follow_up')</th>
                    <th>@lang('lang_v1.action')</th>

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
            { data: 'date', name: 'date' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'mobile', name: 'mobile' },
            { data: 'alternate_number', name: 'alternate_number' },
            { data: 'landline', name: 'landline' },
            { data: 'city', name: 'city' },
            { data: 'district', name: 'district' },
            { data: 'country', name: 'country' },
            { data: 'time_connected', name: 'time_connected' },
            { data: 'note', name: 'crm_activity_details.note' },
            { data: 'next_follow_up_date', name: 'crm_activity_details.next_follow_up_date' },
            { data: 'discontinue_follow_up', name: 'discontinue_follow_up' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    
    var crm_table = $('#crm_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{action("CRMActivityController@index")}}',
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_crm_activity_button', function(e) {
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