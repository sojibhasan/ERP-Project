@extends('layouts.app')
@section('title', __('lang_v1.import_accounts'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.import_accounts')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! Form::open(['url' => action('AccountController@postImportAccounts'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                @if($account_access)
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                {!! Form::file('accounts_csv', ['accept'=> '.xls', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>
                @else
                <div class="text-center" style="color: {{App\System::getProperty('not_enalbed_module_user_color')}}; font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;">
                    <td colspan="4"> {{App\System::getProperty('not_enalbed_module_user_message')}}</td>
                </div>
                @endif

                {!! Form::close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/import_accounts_csv_template.xls') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
                <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('lang_v1.name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>@lang('lang_v1.account_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.account_type_or_sub_type')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('lang_v1.account_group_name')</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>@lang('lang_v1.account_number') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>@lang('lang_v1.opening_balance') </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>@lang('lang_v1.transaction_date') </td>
                        <td>@lang('lang_v1.import_transaction_date_ins')</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>@lang('lang_v1.note') </td>
                        <td></td>
                    </tr>
                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection