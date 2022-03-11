<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'leads::lang.all_districts')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_district_btn"
                    data-href="{{action('\Modules\Leads\Http\Controllers\DistrictController@create')}}"
                    data-container=".district_model">
                    <i class="fa fa-plus"></i> @lang( 'leads::lang.add_district' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="districts_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'leads::lang.date' )</th>
                                <th>@lang( 'leads::lang.district' )</th>
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
<!-- /.content -->