@extends('layouts.app')
@section('title', __('leads::lang.import_leads'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('leads::lang.import_leads')
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
                {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\ImportLeadsController@store'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! Form::label('name', __( 'leads::lang.file_to_import' ) . ':') !!}
                                {!! Form::file('leads_csv', ['accept'=> '.xls', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>

                {!! Form::close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/import_leads_csv_template.xlsx') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
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
                        <td>@lang('leads::lang.date') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.date')</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>@lang('leads::lang.sector') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.sector_ins')</td>
                    </tr>
                 
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.category') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.category_ins')</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>@lang('leads::lang.main_organization') </td>
                        <td>@lang('leads::lang.main_organization_ins')</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>@lang('leads::lang.business') </td>
                        <td>@lang('leads::lang.business_ins')</td>
                    </tr>
                 
                    <tr>
                        <td>4</td>
                        <td>@lang('leads::lang.address') </td>
                        <td>@lang('leads::lang.address_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.town') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.town_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.district') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.district_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.mobile_no_1') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('leads::lang.mobile_no_1_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.mobile_no_2') </td>
                        <td>@lang('leads::lang.mobile_no_2_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.mobile_no_3') </td>
                        <td>@lang('leads::lang.mobile_no_3_ins')</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('leads::lang.land_number') </td>
                        <td>@lang('leads::lang.land_number_ins')</td>
                    </tr>

                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection