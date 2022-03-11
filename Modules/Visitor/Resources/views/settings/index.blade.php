@extends('layouts.app')
@section('title', __('visitor::lang.visitor_registration_settings'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['visitors_registration_setting'])
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#visitor_settings" class="visitor_settings" data-toggle="tab">
                            <strong>@lang('visitor::lang.settings')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['visitors_district'])
                    <li class="@if(session('status.tab') =='town') active @endif">
                        <a href="#district_tab" class="district_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.district')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['visitors_town'])
                    <li class="@if(session('status.tab') =='town_tab') active @endif">
                        <a href="#town_tab" class="town_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.town')</strong>
                        </a>
                    </li>
                    @endif
                  

                </ul>
                <div class="tab-content">
                    @if($permissions['visitors_registration_setting'])
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="visitor_settings">
                        @include('visitor::settings.system.index')
                    </div>
                    @endif
                    @if($permissions['visitors_district'])
                    <div class="tab-pane @if(session('status.tab') =='district_tab') active @endif" id="district_tab">
                        @include('visitor::settings.district.index')
                    </div>
                    @endif
                    @if($permissions['visitors_town'])
                    <div class="tab-pane @if(session('status.tab') =='town_tab') active @endif" id="town_tab">
                        @include('visitor::settings.town.index')
                    </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
    <div class="modal fade gramaseva_vasama_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade balamandalaya_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade default_districts_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    
    <div class="modal fade default_towns_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
        $('body').on('click', '.btn-submit', function(event) {
          event.preventDefault();
          $(this).closest('form').submit();
        });
        other_district_table = $('#other_district_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{action('DefaultDistrictController@index')}}',
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'districts.name'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                
            }
        });
   
    $(document).on('submit', 'form#district_form', function(e){
            
            e.preventDefault();
            
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: "{{action('DefaultDistrictController@store')}}",
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.default_districts_model').modal('hide');
                        toastr.success(result.msg);
                        other_district_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_districts_model ').modal('hide');
                }
            });
        });

         $(document).on('submit', 'form#edit_districts_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        other_district_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.edit_modal ').modal('hide');
                }
            });
        });
        
      $(document).on('click', 'button.delete_district', function(e){
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
        }).then((willDelete) => {
            if (willDelete) {
                var _this = $(this);
                var href = _this.data('href');
                $.ajax({
                    method: "delete",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            other_district_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
        //town
        other_town_table = $('#other_town_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{action('DefaultTownController@index')}}',
            columnDefs:[{
                    "targets": 2,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'towns.name'},
                {data: 'district', name: 'district'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                
            }
        });

        $(document).on('submit', 'form#town_form', function(e){
            
            e.preventDefault();
            
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: "{{action('DefaultTownController@store')}}",
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.default_towns_model').modal('hide');
                        toastr.success(result.msg);
                        other_town_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_towns_model ').modal('hide');
                }
            });
        });

         $(document).on('submit', 'form#edit_town_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.default_towns_model').modal('hide');
                        toastr.success(result.msg);
                        other_town_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_towns_model ').modal('hide');
                }
            });
        });
        
      $(document).on('click', 'button.delete_town', function(e){
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
        }).then((willDelete) => {
            if (willDelete) {
                var _this = $(this);
                var href = _this.data('href');
                $.ajax({
                    method: "delete",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            other_town_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
    //end of town
</script>
@endsection