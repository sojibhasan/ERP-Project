@extends('layouts.app')
@section('title', __('property::lang.import_blocks'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('property::lang.import_blocks')
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
            {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyBlocksController@postImport',
            $id), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
            <div class="row">
                <div class="col-sm-6">
                    <div class="col-sm-8">
                        <div class="form-group">
                            {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                            {!! Form::file('blocks_csv', ['accept'=> '.xls', 'required' => 'required']); !!}
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
                    <a href="{{ asset('files/import_property_blocks_csv_template.xlsx') }}" class="btn btn-success" download><i
                            class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
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
                    <td>@lang('property::lang.block_number') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>@lang('property::lang.block_sale_price') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>@lang('property::lang.block_extent')</td>
                    <td>@lang('property::lang.size')</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>@lang('property::lang.unit') <small class="text-muted">(@lang('lang_v1.required'))</small>
                    </td>
                    <td>@lang('property::lang.unit_name_eg')</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>@lang('property::lang.transaction_date') </td>
                    <td>@lang('property::lang.import_transaction_date_ins') 
                        <br><small class="text-muted">@lang('property::lang.if_empty_will_use_current_date')<small</td>
                </tr>
            </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection