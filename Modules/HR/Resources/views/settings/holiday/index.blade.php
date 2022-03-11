<div class="pos-tab-content @if(session('status.tab') == 'holidays') active @endif">
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __(
                'hr::lang.all_holiday')])
                @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-primary btn-modal pull-right" id="add_holiday_btn"
                        data-href="{{action('\Modules\HR\Http\Controllers\HolidayController@create')}}"
                        data-container=".holiday_model">
                        <i class="fa fa-plus"></i> @lang( 'hr::lang.add_holiday' )</button>
                </div>
                @endslot
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="holiday_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'hr::lang.holiday' )</th>
                                    <th>@lang( 'hr::lang.description' )</th>
                                    <th>@lang( 'hr::lang.start_date' )</th>
                                    <th>@lang( 'hr::lang.end_date' )</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                @endcomponent
            </div>
        </div>
    </section>
</div>