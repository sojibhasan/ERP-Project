<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('award_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('award_location_id', $business_locations, null, ['id' => 'award_location_id', 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title">@lang('hr::lang.employee_award')</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mailbox-controls pull-right">
                                        <button type="button" class="btn btn-block btn-primary btn-modal"
                                            data-href="{{action('\Modules\HR\Http\Controllers\EmployeeAwardController@create')}}"
                                            data-container=".award_modal">
                                            <i class="fa fa-plus"></i> @lang('messages.add' )</button>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <table id="award_list_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('hr::lang.employee_number')</th>
                                        <th>@lang('hr::lang.employee_name')</th>
                                        <th>@lang('hr::lang.award_name')</th>
                                        <th>@lang('hr::lang.gift_item')</th>
                                        <th>@lang('hr::lang.award_amount')</th>
                                        <th>@lang('hr::lang.month')</th>
                                        <th style="width:125px;">@lang('hr::lang.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>