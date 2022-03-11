<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.all_member_group')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_member_group_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\MemberGroupController@create')}}"
                    data-container=".member_group_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="member_group_table"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'member::lang.date' )</th>
                                <th>@lang( 'member::lang.member_group' )</th>
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