<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'tasksmanagement::lang.all_priorities')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_priority_btn"
                    data-href="{{action('\Modules\TasksManagement\Http\Controllers\PriorityController@create')}}"
                    data-container=".priority_model">
                    <i class="fa fa-plus"></i> @lang( 'tasksmanagement::lang.add_priority' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="priority_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'tasksmanagement::lang.date' )</th>
                                <th>@lang( 'tasksmanagement::lang.name' )</th>
                                <th>@lang( 'tasksmanagement::lang.added_by' )</th>
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