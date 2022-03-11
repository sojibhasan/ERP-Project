<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('ran::lang.all_your_list_goldsmiths')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Ran\Http\Controllers\GoldSmithController@create')}}"
                data-container=".goldsmith_model">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="goldsmith_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('ran::lang.name')</th>
                    <th>@lang('ran::lang.email')</th>
                    <th>@lang('ran::lang.mobile')</th>
                    <th>@lang('ran::lang.landline')</th>
                    <th>@lang('ran::lang.employee_number')</th>
                    <th>@lang('ran::lang.opening_qty')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->