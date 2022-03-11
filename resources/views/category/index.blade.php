@extends('layouts.app')
@section('title', 'Categories')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'category.categories' )
        <small>@lang( 'category.manage_your_categories' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'category.manage_your_categories' )])
        @can('category.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('CategoryController@create')}}" 
                    data-container=".category_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('category.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="all_category_table">
                    <thead>
                        <tr>
                            <th>@lang( 'category.category' )</th>
                            <th>@lang( 'category.code' )</th>
                            <th>@lang( 'category.sub_category' )</th>
                            <th>@lang( 'category.sub_cat_code' )</th>
                            <th>@lang( 'category.cogs' )</th>
                            <th>@lang( 'category.sales_accounts' )</th>
                            <th class="notexport">@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade category_modal" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script>
        $(document).ready(function(){
            category_table = $('#all_category_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/categories',
                   
                },
                columns: [
                    { data: 'category_name', name: 'name' },
                    { data: 'category_short_code', name: 'short_code' },
                    { data: 'sub_category_name', name: 'name' },
                    { data: 'sub_category_short_code', name: 'short_code' },
                    { data: 'cogs', name: 'cogs' },
                    { data: 'sales_accounts', name: 'sales_accounts' },
                    { data: 'action', name: 'action' },
                ],
                @include('layouts.partials.datatable_export_button')
              
            });
        })
    </script>
@endsection
