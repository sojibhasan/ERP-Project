@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Superadmin Settings')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.super_admin_settings')<small>@lang('superadmin::lang.edit_super_admin_settings')</small>
    </h1>
</section>
<style>
    .wrapper {
        overflow: hidden;
    }
</style>
<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@update', 'method' =>
    'put' , 'id' => 'setting_form', 'enctype' => 'multipart/form-data']) !!}
    <div class="row">
        <div class="col-xs-12">
            <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#"
                            class="list-group-item text-center @if(!session('status.account_default') && !session('status.manage_user') && !session('status.role') && !session('status.tank_dip_chart')) active @endif">@lang('superadmin::lang.super_admin_settings')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.application_settings')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.email_smtp_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.payment_gateways')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.sms')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.backup')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.cron')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.login')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.banners')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.give_away_gifts')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.patient')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.hospital')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.pharmacy')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.laboratory')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.package_variables')</a>
                        <a href="#"
                            class="list-group-item text-center @if(session('status.account_default')) active @endif">@lang('superadmin::lang.default_accounts')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.company_package_variables')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.business_categories')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.default_mapping_accounts')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.hr_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.visitor_settings')</a>
                        <a href="#"
                            class="list-group-item text-center @if(session('status.manage_user')) active @endif">@lang('superadmin::lang.users')</a>
                        <a href="#"
                            class="list-group-item text-center @if(session('status.role')) active @endif">@lang('superadmin::lang.role')</a>
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tank_dip_chart')) active @endif">@lang('superadmin::lang.tank_dip_chart')</a>
                        <a href="#" class="list-group-item text-center">@lang('superadmin::lang.default_messages')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.edit_account_entries')</a>
                        <a href="#"
                            class="list-group-item text-center">@lang('superadmin::lang.edit_contact_entries')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('superadmin::superadmin_settings.partials.super_admin_settings')
                    @include('superadmin::superadmin_settings.partials.application_settings')
                    @include('superadmin::superadmin_settings.partials.email_smtp_settings')
                    @include('superadmin::superadmin_settings.partials.payment_gateways')
                    @include('superadmin::superadmin_settings.partials.sms')
                    @include('superadmin::superadmin_settings.partials.backup')
                    @include('superadmin::superadmin_settings.partials.cron')
                    @include('superadmin::superadmin_settings.partials.login_page_settings')
                    @include('superadmin::superadmin_settings.partials.banners')
                    @include('superadmin::superadmin_settings.give_away_gifts.index')
                    @include('superadmin::superadmin_settings.partials.patient')
                    @include('superadmin::superadmin_settings.partials.hospital')
                    @include('superadmin::superadmin_settings.partials.pharmacy')
                    @include('superadmin::superadmin_settings.partials.laboratory')
                    @include('superadmin::superadmin_settings.partials.package_variables')
                    @include('superadmin::superadmin_settings.partials.default_accounts')
                    @include('superadmin::superadmin_settings.partials.company_package_variables')
                    @include('superadmin::superadmin_settings.partials.business_categories')
                    @include('superadmin::superadmin_settings.partials.default_mapping_accounts')
                    @include('superadmin::superadmin_settings.hr_settings.index')
                    @include('superadmin::superadmin_settings.visitor_settings.index')
                    @include('superadmin::superadmin_settings.manage_user.index')
                    @include('superadmin::superadmin_settings.role.index')
                    @include('superadmin::superadmin_settings.tank_dip_chart.index')
                    @include('superadmin::superadmin_settings.partials.default_messages')
                    @include('superadmin::superadmin_settings.partials.edit_account_entries')
                    @include('superadmin::superadmin_settings.partials.edit_contact_entries')
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group pull-right">
                    {{Form::submit('update', ['class'=>"btn btn-danger", 'id' => 'setting_submit'])}}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    <div class="modal fade edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade default_account_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade default_account_type_model" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade default_account_group_model" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel"></div>

    <div class="modal fade gramaseva_vasama_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade balamandalaya_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade default_districts_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade default_towns_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


    <!-- Modal -->
    <div id="department_modal" class="modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('hr::lang.add_department')</h4>
                </div>
                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\DepartmentController@store'), 'method' =>
                'post']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="department">@lang('hr::lang.department'):</label>
                                <input type="text" class="form-control" name="department">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">@lang('hr::lang.description'):</label>
                                <textarea type="text" class="form-control" name="description"> </textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_superadmin_default" value="1">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>

    <div class="modal fade department_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


    <!-- Modal -->
    <div id="job_title_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('hr::lang.add_job_title')</h4>
                </div>
                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\JobtitleController@store'), 'method' =>
                'post']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="job_title">@lang('hr::lang.job_title'):</label>
                                <input type="text" class="form-control" name="job_title">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">@lang('hr::lang.description'):</label>
                                <textarea type="text" class="form-control" name="description"> </textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_superadmin_default" value="1">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>

    <div class="modal fade jobtitle_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>


    <!-- Modal -->
    <div id="job_category_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('hr::lang.add_job_category')</h4>
                </div>
                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\JobCategoryController@store'), 'method' =>
                'post']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="job_title">@lang('hr::lang.job_category'):</label>
                                <input type="text" class="form-control" name="category_name">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="is_superadmin_default" value="1">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>

    <div class="modal fade jobcategory_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- Modal -->
    <div id="working_shift_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('hr::lang.add_workshift')</h4>
                </div>
                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\WorkShiftController@store'), 'method' =>
                'post']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="shift_name">@lang('hr::lang.shift_name'):</label>
                                <input type="text" class="form-control" name="shift_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shift_form">@lang('hr::lang.shift_form'):</label>
                                <input type="text" id="shif_from" class="form-control" name="shift_form">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shift_to">@lang('hr::lang.shift_to'):</label>
                                <input type="text" id="shif_to" class="form-control" name="shift_to">
                            </div>
                        </div>
                        <input type="hidden" name="is_superadmin_default" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>

    <div class="modal fade workshift_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade holiday_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade leave_application_type_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade salary_grade_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade employment_status_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade salary_component_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade tax_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade religion_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade tank_dip_chart_model" role="dialog" aria-labelledby="gridSystemModalLabel">

        <div class="modal fade give_away_gift_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <input type="hidden" name="is_superadmin_page" id="is_superadmin_page" value="1">
</section>
@stop
@section('javascript')
<script src="{{url('Modules/HR/Resources/assets/js/app.js')}}"></script>
<script type="text/javascript">
    $(document).on('change', '#BACKUP_DISK', function() {
        if($(this).val() == 'dropbox'){
            $('div#dropbox_access_token_div').removeClass('hide');
        } else {
            $('div#dropbox_access_token_div').addClass('hide');
        }
    });

    $(document).ready( function(){

        var img_fileinput_setting = {
            showUpload: false,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
            previewSettings: {
                image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
            },
        };
        $('#upload_image').fileinput(img_fileinput_setting);

        if ($('#welcome_email_body').length) {
            tinymce.init({
                selector: 'textarea#welcome_email_body',
            });
        }
        if ($('#customer_welcome_email_body').length) {
            tinymce.init({
                selector: 'textarea#customer_welcome_email_body',
            });
        }
        if ($('#agent_welcome_email_body').length) {
            tinymce.init({
                selector: 'textarea#agent_welcome_email_body',
            });
        }
        if ($('#new_subscription_email_body').length) {
            tinymce.init({
                selector: 'textarea#new_subscription_email_body',
            });
        }
        if ($('#new_subscription_email_body_offline').length) {
            tinymce.init({
                selector: 'textarea#new_subscription_email_body_offline',
            });
        }
        if ($('#visitor_welcome_email_body').length) {
            tinymce.init({
                selector: 'textarea#visitor_welcome_email_body',
            });
        }
        if ($('#welcome_msg_body').length) {
            tinymce.init({
                selector: 'textarea#welcome_msg_body',
            });
        }
        if ($('#patient_register_success_msg').length) {
            tinymce.init({
                selector: 'textarea#patient_register_success_msg',
            });
        }
        if ($('#company_register_success_msg').length) {
            tinymce.init({
                selector: 'textarea#company_register_success_msg',
            });
        }
        if ($('#visitor_register_success_msg').length) {
            tinymce.init({
                selector: 'textarea#visitor_register_success_msg',
            });
        }
        if ($('#customer_register_success_msg').length) {
            tinymce.init({
                selector: 'textarea#customer_register_success_msg',
            });
        }
        if ($('#member_register_success_msg').length) {
            tinymce.init({
                selector: 'textarea#member_register_success_msg',
            });
        }
        if ($('#subscription_message_online_success_msg').length) {
            tinymce.init({
                selector: 'textarea#subscription_message_online_success_msg',
            });
        }
        if ($('#subscription_message_offline_success_msg').length) {
            tinymce.init({
                selector: 'textarea#subscription_message_offline_success_msg',
            });
        }
    });
    $('#setting_submit').click(function(){
        $('#setting_form').submit();
    });
</script>


<script>
    var package_variables_table = $('#package_variables_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: "{{action('\Modules\Superadmin\Http\Controllers\PackageVariableController@index')}}",
          data: function(d) {
          }
      },
      columnDefs: [
          {
              targets: 6,
              orderable: false,
              searchable: false,
          },
      ],
      columns: [
          { data: 'variable_code', name: 'variable_code' },
          { data: 'variable_options', name: 'variable_options' },
          { data: 'option_value', name: 'option_value' },
          { data: 'increase_decrease', name: 'increase_decrease' },
          { data: 'variable_type', name: 'variable_type' },
          { data: 'price_value', name: 'price_value' },
          { data: 'action', name: 'action' },
        
      ],
  });

    var company_package_variables_table = $('#company_package_variables_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: "{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@index')}}",
          data: function(d) {
          }
      },
      columnDefs: [
          {
              targets: 6,
              orderable: false,
              searchable: false,
          },
      ],
      columns: [
          { data: 'variable_code', name: 'variable_code' },
          { data: 'variable_options', name: 'variable_options' },
          { data: 'option_value', name: 'option_value' },
          { data: 'increase_decrease', name: 'increase_decrease' },
          { data: 'variable_type', name: 'variable_type' },
          { data: 'price_value', name: 'price_value' },
          { data: 'action', name: 'action' },
        
      ],
  });
  
</script>

<script>
    $(document).on('click', '#package_variable_add', function(e) {
        e.preventDefault();
        variable_options = $("#variable_options").val();
        variable_code = $("#variable_code").val();
        option_value = $("#option_value").val();
        increase_decrease = $("#increase_decrease").val();
        variable_type = $("#variable_type").val();
        price_value = $("#price_value").val();
        
          $.ajax({
              method: 'post',
              url: "{{action('\Modules\Superadmin\Http\Controllers\PackageVariableController@store')}}",
              data:  {
                    'variable_options': variable_options,
                    'variable_code': variable_code,
                    'option_value': option_value,
                    'increase_decrease': increase_decrease,
                    'variable_type': variable_type,
                    'price_value': price_value,
              },
              success: function(result) {
                  console.log(result);
                  if(result.success == 1){
                      toastr.success(result.msg);
                      package_variables_table.ajax.reload();
                        $("#variable_options").val('');
                        $("#option_value").val('');
                        $("#increase_decrease").val('');
                        $("#variable_type").val('');
                        $("#price_value").val('');
                  }else{
                      toastr.error(result.msg);
                      package_variables_table.ajax.reload();
                  }
              },
          });
      });
    $(document).on('click', '#company_package_variable_add', function(e) {
        e.preventDefault();
        company_variable_options = $("#company_variable_options").val();
        company_variable_code = $("#company_variable_code").val();
        company_option_value = $("#company_option_value").val();
        company_increase_decrease = $("#company_increase_decrease").val();
        company_variable_type = $("#company_variable_type").val();
        company_price_value = $("#company_price_value").val();
        
          $.ajax({
              method: 'post',
              url: "{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@store')}}",
              data:  {
                    'variable_options': company_variable_options,
                    'variable_code': company_variable_code,
                    'option_value': company_option_value,
                    'increase_decrease': company_increase_decrease,
                    'variable_type': company_variable_type,
                    'price_value': company_price_value,
              },
              success: function(result) {
                  if(result.success == 1){
                      toastr.success(result.msg);
                      company_package_variables_table.ajax.reload();
                        $("#company_variable_options").val('');
                        $("#company_option_value").val('');
                        $("#company_increase_decrease").val('');
                        $("#company_variable_type").val('');
                        $("#company_price_value").val('');
                  }else{
                      toastr.error(result.msg);
                      company_package_variables_table.ajax.reload();
                  }
              },
          });
      });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', 'a.delete_variable', function(e){
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
                                package_variables_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function(){
        $(document).on('click', 'a.delete_company_variable', function(e){
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
                                company_package_variables_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });

</script>


<script>
    $(document).ready(function(){

        $(document).on('submit', 'form#edit_payment_account_form', function(e){
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
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_account_model ').modal('hide');
                }
            });
        });

        $(document).on('submit', 'form#payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: "{{action('DefaultAccountController@store')}}",
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_account_model ').modal('hide');
                }
            });
        });


        other_account_table = $('#other_account_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/default-account?account_type=other',
            columnDefs:[{
                    "targets": 5,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'default_accounts.name'},
                {data: 'parent_account_type_name', name: 'pat.name'},
                {data: 'account_type_name', name: 'ats.name'},
                {data: 'account_number', name: 'default_accounts.account_number'},
                {data: 'group_name', name: 'default_account_groups.name'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                
            }
        });
        $(document).on('click', 'button.delete_account', function(e){
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
                                other_account_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
   
        $(document).on('click', 'button.delete_account_type', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $(this).closest('form').submit();
                }
            });
        });

        business_categories = $('#business_categories').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{action('BusinessCategoryController@index')}}",
                    data: function(d) {
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false,
                        "width" : "30%",
                    }],
                columns: [
                    {data: 'category_name', name: 'category_name'},
                    {data: 'action', name: 'action'}
                ],
                "fnDrawCallback": function (oSettings) {
                    
                }
        });
        $('body').on('submit', '#business_category_form' ,function(e){
            e.preventDefault();
            data = $(this).serialize();
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                data: data,
                success: function(result) {
                    if(result.success == true){
                        toastr.success(result.msg);
                        business_categories.ajax.reload();
                    }else{
                        toastr.success(result.msg);
                    }

                    $('.edit_modal').modal('hide');
                },
            });
        });

        $(document).on('click', '.delete_busiess_cat_button', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var _this = $(this);
                    var href = _this.attr('href');
                    $.ajax({
                        method: "delete",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                business_categories.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

      // account_groups_table
      account_groups_table = $('#account_groups_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '/default-account-group',
            columnDefs:[{
                    "targets": 3,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'account_groups.name'},
                {data: 'account_type_name', name: 'ats.name'},
                {data: 'note', name: 'note'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', '#save_account_group_btn', function(e){
            e.preventDefault();
            let name = $('#account_group_name_group').val();
            let account_type_id = $('#account_type_id_group').val();
            let note = $('#note_group').val();

            $.ajax({
                method: 'post',
                url: '/default-account-group',
                data: { 
                    name,
                    account_type_id,
                    note,
                },
                success: function(result) {
                    if(result.success == 1){
                        toastr.success(result.msg);
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_account_model').modal('hide');
                    account_groups_table.ajax.reload();
                },
            });

        });
        $(document).on('click', '#update_account_group_btn', function(e){
            e.preventDefault();
            let name = $('#account_group_name_group').val();
            let account_type_id = $('#account_type_id_group').val();
            let note = $('#note_group').val();
            let url = $('#account_group_form').attr('action');
            $.ajax({
                method: 'put',
                url: url,
                data: { 
                    name,
                    account_type_id,
                    note,
                },
                success: function(result) {
                    if(result.success == 1){
                        toastr.success(result.msg);
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.default_account_model').modal('hide');
                    account_groups_table.ajax.reload();
                },
            });

        });

        $(document).on('click', 'button.account_group_delete', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            account_groups_table.ajax.reload();
                        },
                    });
                }
            });
        });
        give_away_gift_table = $('#give_away_gift_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/give-away-gifts',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
                
            }
        });

        $(document).on('click', 'button#give_away_gift_add', function(e){
            let name = $('#gift_name').val();
            $.ajax({
                method: 'post',
                url: '/superadmin/give-away-gifts',
                data: { name },
                success: function(result) {
                    if(result.success){
                        toastr.success(result.msg);
                        give_away_gift_table.ajax.reload();
                        $('#gift_name').val('');
                    }else{
                        toastr.error(result.msg);
                    }
                },
            });

        });

        $(document).on('submit', 'form#give_away_gift_edit_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: 'put',
                url: $(this).attr('action'),
                data: data,
                dataType: 'json',
                success: function(result) {
                    if(result.success){
                        toastr.success(result.msg);
                        give_away_gift_table.ajax.reload();
                        $('.view_modal').modal('hide');
                    }else{
                        toastr.error(result.msg);
                    }
                },
            });
        });
        
        $(document).on('click', 'button.delete_give_away_gift_button', function(e){
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
                                give_away_gift_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>

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

<script type="text/javascript">
    //User table
    $(document).ready( function(){
        var users_table = $('#users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{action('\Modules\Superadmin\Http\Controllers\DefaultManageUserController@index')}}",
            columnDefs: [ {
                "targets": [4],
                "orderable": false,
                "searchable": false
            } ],
            "columns":[
                {"data":"username"},
                {"data":"full_name"},
                {"data":"role"},
                {"data":"email"},
                {"data":"action"}
            ]
        });
        $(document).on('click', 'button.delete_user_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_user,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
             });
        });
        
    });
    
    
</script>

<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var roles_table = $('#roles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@index')}}",
            buttons:[],
            columnDefs: [ {
                "targets": 1,
                "orderable": false,
                "searchable": false
            } ]
        });
        $(document).on('click', 'button.delete_role_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_role,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                roles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });


    $(document).ready( function(){
        const pickr = Pickr.create({
            el: '.color-picker',
            theme: 'classic', // or 'monolith', or 'nano'
            default: @if(!empty($settings['visitor_code_color']))"{{$settings['visitor_code_color']}}" @else '#000000' @endif,
            position: 'top-middle',
            swatches: [
                'rgba(244, 67, 54, 1)',
                'rgba(233, 30, 99, 0.95)',
                'rgba(156, 39, 176, 0.9)',
                'rgba(103, 58, 183, 0.85)',
                'rgba(63, 81, 181, 0.8)',
                'rgba(33, 150, 243, 0.75)',
                'rgba(3, 169, 244, 0.7)',
                'rgba(0, 188, 212, 0.7)',
                'rgba(0, 150, 136, 0.75)',
                'rgba(76, 175, 80, 0.8)',
                'rgba(139, 195, 74, 0.85)',
                'rgba(205, 220, 57, 0.9)',
                'rgba(255, 235, 59, 0.95)',
                'rgba(255, 193, 7, 1)'
            ],

            components: {

                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    input: true,
                    clear: true,
                    save: true,
                    useAsButton: false,
                }
            }
        }).on('save', (color, instance) => {
            $('#visitor_code_color').val(color.toHEXA().toString());
        });
    });


    //tank dip chart
    // if ($('#tank_dip_chart_date_range').length == 1) {
    //     $('#tank_dip_chart_date_range').daterangepicker(dateRangeSettings, function(start, end) {
    //         $('#tank_dip_chart_date_range').val(
    //             start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
    //         );
    //     });
    //     $('#tank_dip_chart_date_range').on('cancel.daterangepicker', function(ev, picker) {
    //         $('#tank_dip_chart_date_range').val('');
    //     });
    //     $('#tank_dip_chart_date_range')
    //         .data('daterangepicker')
    //         .setStartDate(moment().startOf('month'));
    //     $('#tank_dip_chart_date_range')
    //         .data('daterangepicker')
    //         .setEndDate(moment().endOf('month'));
    // }
    $(document).ready( function(){
        var tank_dip_chart_table = $('#tank_dip_chart_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url : "{{action('\Modules\Superadmin\Http\Controllers\TankDipChartController@index')}}",
                data: function(d){
                    d.sheet_name = $('#filter_sheet_name').val();
                    d.tank_manufacturer = $('#filter_tank_manufacturer').val();
                    d.tank_capacity = $('#filter_tank_capacity').val();
                }
            },
            buttons:[],
            columnDefs: [ {
                "targets": 2,
                "orderable": false,
                "searchable": false
            } ],
             "columns":[
                {"data":"dip_reading"},
                {"data":"dip_reading_value"},
                {"data":"action"}
            ]
        });

        $('#filter_sheet_name, #filter_tank_manufacturer, #filter_tank_capacity').change(function(){
            tank_dip_chart_table.ajax.reload();
        })

        $(document).on('click', 'button.delete_tank_dip_chart_button', function(){
            swal({
              title: LANG.sure,
              text: 'This detail will be deleted',
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                tank_dip_chart_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });

    //edit account entries script
    if ($('#edit_account_date_range').length == 1) {
        $('#edit_account_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#edit_account_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#edit_account_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#edit_account_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#edit_account_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }


        $(document).ready( function(){
            $('#business_id option:eq(0)').attr('selected', true).trigger('change');
        });


        $('#transaction_type, #account_id, #edit_account_date_range, #debit_credit').change( function(){
          account_book.ajax.reload();
        });
        
        $('#business_id').change(function () {
            $.ajax({
                method: 'get',
                url: '/superadmin/get-account-drop-down-by-business/'+$(this).val(),
                data: {  },
                contentType: 'html',
                success: function(result) {
                        $('#account_id').empty().append(result);
                        $('#account_id option:eq(1)').attr('selected', true)
                        account_book();
                },
            });
        })

        function account_book() {
            account_book = $('#account_book').DataTable({
              processing: true,
              serverSide: true,
              pageLength: 25,
              aaSorting: [0,'asc'],
              ajax: {
                url: '{{action("\Modules\Superadmin\Http\Controllers\EditAccountEntriesController@index")}}',
                data: function(d) {
                  var start = '';
                  var end = '';
                  if($('#edit_account_date_range').val()){
                    start = $('input#edit_account_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#edit_account_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                  }
                  d.start_date = start;
                  d.end_date = end;
                  d.transaction_type = $('#transaction_type').val();
                  d.debit_credit = $('#debit_credit').val();
                  d.account_id = $('#account_id').val();
                  d.business_id = $('#business_id').val();
    
                }
              },
              aaSorting: [[0, 'asc']],
              "ordering": true,
              "searching": true,
              columns: [
                {data: 'action', name: 'action'},
                {data: 'operation_date', name: 'operation_date'},
                {data: 'description', name: 'description'},
                {data: 'note', name: 'note'},
                {data: 'attachment', name: 'attachment'},
                {data: 'added_by', name: 'u.first_name'},
                {data: 'debit', name: 'amount'},
                {data: 'credit', name: 'amount'},
                {data: 'balance', name: 'balance', searchable: false},
              ],
              @include('layouts.partials.datatable_export_button')
              "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#account_book'));
              }
            });
        }

        $(document).ready( function(){
            list_edit_account_book = $('#list_edit_account_book').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            aaSorting: [0,'asc'],
            ajax: {
                url: '{{action("\Modules\Superadmin\Http\Controllers\EditAccountEntriesController@listEditAccountTransaction")}}',
                data: function(d) {
                
                }
            },
            columns: [
                {data: 'date_and_time', name: 'date_and_time'},
                {data: 'business_name', name: 'business.name'},
                {data: 'account_name', name: 'accounts.name'},
                {data: 'orignal_amount', name: 'orignal_amount'},
                {data: 'edited_amount', name: 'edited_amount'},
                {data: 'action_type', name: 'action_type'},
            
            ],
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#list_edit_account_book'));
            }
            });
        });

        $(document).on('click', 'a.delete_account_transaction', function(e){
            e.preventDefault();
            swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                url: href,
                method: 'DELETE',
                dataType: "json",
                success: function(result){
                    if(result.success === true){
                    toastr.success(result.msg);
                    list_edit_account_book.ajax.reload();
                    account_book.ajax.reload();
                    } else {
                    toastr.error(result.msg);
                    }
                }
                });
            }
            });
        });

        //edit contact entries script
        if ($('#edit_contact_date_range').length == 1) {
            $('#edit_contact_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#edit_contact_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#edit_contact_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#edit_contact_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#edit_contact_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }


        $(document).ready( function(){
            $('#contact_business_id option:eq(0)').attr('selected', true).trigger('change');
        });


        $('#transaction_type, #contact_id, #edit_contact_date_range, #debit_credit').change( function(){
          contact_ledger.ajax.reload();
        });
        
        $('#contact_business_id, #contact_type').change(function () {
            $.ajax({
                method: 'get',
                url: '/superadmin/get-contact-drop-down-by-business/'+$('#contact_business_id').val()+'/'+$('#contact_type').val(),
                data: {  },
                contentType: 'html',
                success: function(result) {
                        $('#contact_id').empty().append(result);
                        $('#contact_id option:eq(1)').attr('selected', true)
                        contact_ledger.ajax.reload();
                },
            });
        })
        $(document).ready( function(){
        // function contact_ledger() {
            contact_ledger = $('#contact_ledger').DataTable({
              processing: true,
              serverSide: true,
              pageLength: 25,
              aaSorting: [0,'asc'],
              ajax: {
                url: '{{action("\Modules\Superadmin\Http\Controllers\EditContactEntriesController@getLedger")}}',
                data: function(d) {
                  var start = '';
                  var end = '';
                  if($('#edit_contact_date_range').val()){
                    start = $('input#edit_contact_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#edit_contact_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                  }
                  d.start_date = start;
                  d.end_date = end;
                  d.debit_credit = $('#contact_debit_credit').val();
                  d.contact_id = $('#contact_id').val();
                  d.business_id = $('#contact_business_id').val();
    
                }
              },
              aaSorting: [[0, 'asc']],
              "ordering": true,
              "searching": true,
              columns: [
                {data: 'action', name: 'action'},
                {data: 'operation_date', name: 'operation_date'},
                {data: 'ref_no', name: 'transactions.ref_no'},
                {data: 'type', name: 'transactions.type'},
                {data: 'location_name', name: 'business_locations.name'},
                {data: 'payment_status', name: 'payment_status'},
                {data: 'debit', name: 'amount'},
                {data: 'credit', name: 'amount'},
                {data: 'balance', name: 'balance', searchable: false},
                {data: 'cheque_number', name: 'cheque_number'},
                {data: 'payment_method', name: 'transaction_payments.method'},
              ],
              @include('layouts.partials.datatable_export_button')
              "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#contact_ledger'));
              }
            });
            });
       //}

        $(document).ready( function(){
            list_edit_contact_ledger = $('#list_edit_contact_ledger').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                aaSorting: [0,'asc'],
                ajax: {
                    url: '{{action("\Modules\Superadmin\Http\Controllers\EditContactEntriesController@index")}}',
                    data: function(d) {
                    
                    }
                },
                columns: [
                    {data: 'date_and_time', name: 'date_and_time'},
                    {data: 'business_name', name: 'business.name'},
                    {data: 'contact_name', name: 'contacts.name'},
                    {data: 'orignal_amount', name: 'orignal_amount'},
                    {data: 'edited_amount', name: 'edited_amount'},
                    {data: 'action_type', name: 'action_type'},
                
                ],
                
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#list_edit_contact_ledger'));
                }
            });
        });

        $(document).on('click', 'a.delete_contact_transaction', function(e){
            e.preventDefault();
            swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                url: href,
                method: 'DELETE',
                dataType: "json",
                success: function(result){
                    if(result.success === true){
                        toastr.success(result.msg);
                        list_edit_contact_ledger.ajax.reload();
                        contact_ledger.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
                });
            }
            });
        });
</script>
@endsection