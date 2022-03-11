
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('mpcs::lang.list_opening_values')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lov_date_range', __('lang_v1.date') . ':') !!}
                    {!! Form::text('lov_date_range',  null, ['class' => 'form-control', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lov_form_name', __('mpcs::lang.form_name') . ':') !!}
                    {!! Form::select('lov_form_name', ['9C' => '9C', 'F159ABC' =>'F159ABC', 'F16A' => 'F16A', 'F21C' => 'F21C'], null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['action' => '\Modules\MPCS\Http\Controllers\FormsSettingController@store', 'method' =>
            'post', 'id' =>
            'form_list_opening_values']) !!}
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-12">
                    <div id="msg"></div>
                    <table id="lov_table" class="table table-striped table-bordered" cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th>@lang('mpcs::lang.action')</th>
                                <th>@lang('mpcs::lang.date')</th>
                                <th>@lang('mpcs::lang.form_name')</th>
                                <th>@lang('mpcs::lang.edited_by')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
            {!! Form::close() !!}
        </div>
    </div>
    <div class="modal fade forms_setting_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->