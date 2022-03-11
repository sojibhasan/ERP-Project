<div class="pos-tab-content @if(session('status.account_default')) active @endif">

    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('account.default_payment_accounts')
            <small>@lang('account.manage_default_account')</small>
        </h1>
    </section>
    
    <!-- Main content -->
    <section class="content">
        @can('account.access')
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="@if(!session('status.account_default')) active @endif">
                            <a href="#other_accounts" data-toggle="tab">
                                <i class="fa fa-book"></i> <strong>@lang('account.accounts')</strong>
                            </a>
                        </li>
                        <li class="@if(session('status.account_default')) active @endif">
                            <a href="#account_types" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                @lang('lang_v1.account_types') </strong>
                            </a>
                        </li>
                        <li class="@if(session('status.account_default_group')) active @endif">
                            <a href="#account_groups" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                @lang('account.account_group') </strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane @if(!session('status.account_default')) active @endif" id="other_accounts">
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="button" id="add_button" class="btn btn-primary btn-modal pull-right" 
                                        data-container=".default_account_model"
                                        data-href="{{action('DefaultAccountController@create')}}">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                </div>
                                <div class="col-sm-12">
                                <br>
                                    <table class="table table-bordered table-striped" id="other_account_table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'lang_v1.name' )</th>
                                                <th>@lang( 'lang_v1.account_type' )</th>
                                                <th>@lang( 'lang_v1.account_sub_type' )</th>
                                                <th>@lang('account.account_number')</th>
                                                <th>@lang('account.account_group')</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane @if(session('status.account_default')) active @endif" id="account_types">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-modal pull-right" 
                                        data-href="{{action('DefaultAccountTypeController@create')}}"
                                        data-container=".default_account_type_model">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered" id="account_types_table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'lang_v1.name' )</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($default_account_types as $account_type)
                                                <tr class="account_type_{{$account_type->id}}">
                                                    <th>{{$account_type->name}}</th>
                                                    <td>
                                                        
                                                        {!! Form::open(['url' => action('DefaultAccountTypeController@destroy', $account_type->id), 'method' => 'delete' ]) !!}
                                                        <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                        data-href="{{action('DefaultAccountTypeController@edit', $account_type->id)}}"
                                                        data-container=".default_account_type_model">
                                                        <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
    
                                                        <button type="button" class="btn btn-danger btn-xs delete_account_type">
                                                        <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                                        {!! Form::close() !!}
                                                    </td>
                                                </tr>
                                                @foreach($account_type->sub_types as $sub_type)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;-- {{$sub_type->name}}</td>
                                                        <td>
                                                            
    
                                                            {!! Form::open(['url' => action('DefaultAccountTypeController@destroy', $sub_type->id), 'method' => 'delete' ]) !!}
                                                                <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                            data-href="{{action('DefaultAccountTypeController@edit', $sub_type->id)}}"
                                                            data-container=".default_account_type_model">
                                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                                <button type="button" class="btn btn-danger btn-xs delete_account_type">
                                                                <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                                                {!! Form::close() !!}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane @if(session('status.account_default_group')) active @endif" id="account_groups">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-modal pull-right" id="add_acount_group_btn"
                                        data-href="{{action('DefaultAccountGroupController@create')}}"
                                        data-container=".default_account_group_model">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered" id="account_groups_table"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'lang_v1.name' )</th>
                                                <th>@lang( 'lang_v1.account_type_name' )</th>
                                                <th>@lang( 'lang_v1.note' )</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        
        <div class="modal fade account_model" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
        </div>
    
        <div class="modal fade" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel" id="account_type_modal">
        </div>
       
    </section>
    <!-- /.content -->
    
   
  
   
</div>