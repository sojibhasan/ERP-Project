@extends('layouts.app')
@section('title', __('lang_v1.merged_sub_categories'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.merged_sub_categories' )
        <small>@lang( 'lang_v1.manage_your_merged_sub_categories' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_merged_sub_categories' )])
        @can('category.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('MergedSubCategoryController@create')}}" 
                    data-container=".category_modal">
                    <i class="fa fa-compress"></i> @lang( 'lang_v1.merge_sub_category' )</button>
                </div>
            @endslot
        @endcan
        @can('category.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="merged_sub_category_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.date_and_time' )</th>
                            <th>@lang( 'category.category' )</th>
                            <th>@lang( 'lang_v1.merged_sub_category_name' )</th>
                            <th>@lang( 'lang_v1.merged_sub_categories' )</th>
                            <th>@lang( 'lang_v1.status' )</th>
                            <th>@lang( 'lang_v1.user' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade category_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).on('click', 'button.add_merged_sub_category', function() {
        $.ajax({
            method: 'post',
            url: "{{action('MergedSubCategoryController@store')}}",
            data: { 
                date_and_time : $('#date_and_time').val(),
                merged_sub_category_name : $('#merged_sub_category_name').val(),
                category_id : $('#category').val(),
                sub_categories : $('#sub_categories').val(),
                status : $('#status').val()
             },
            success: function(result) {
                if(result.success == 1){
                    toastr.success(result.msg);
                }else{
                    toastr.success(result.msg);
                }
                merged_sub_category_table.ajax.reload();
                $('.category_modal').modal('hide');
            },
        });
    });
    $(document).on('click', 'button.edit_merged_sub_category', function() {
        $.ajax({
            method: 'put',
            url: "/merged-sub-category/"+$('#merge_id').val(),
            data: { 
                date_and_time : $('#date_and_time').val(),
                merged_sub_category_name : $('#merged_sub_category_name').val(),
                category_id : $('#category').val(),
                sub_categories : $('#sub_categories').val(),
                status : $('#status').val()
             },
            success: function(result) {
                if(result.success == 1){
                    toastr.success(result.msg);
                }else{
                    toastr.success(result.msg);
                }
                merged_sub_category_table.ajax.reload();
                $('.category_modal').modal('hide');
            },
        });
    });

    $(document).on('click', 'a.delete_merge_button', function(e) {
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
                        merged_sub_category_table.ajax.reload();
                    },
                });
            }
        });
    });


    var columns = [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'category_name', name: 'category_name' },
            { data: 'merged_sub_category_name', name: 'merged_sub_category_name' },
            { data: 'merged_sub_categories', name: 'merged_sub_categories' },
            { data: 'status', name: 'status' },
            { data: 'username', name: 'username' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
        merged_sub_category_table = $('#merged_sub_category_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{action('MergedSubCategoryController@index')}}',
        columnDefs: [ {
            "targets": 6,
            "orderable": false,
            "searchable": false
        } ],
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });
</script>
@endsection
