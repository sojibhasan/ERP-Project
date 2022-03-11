<div class="pos-tab-content @if(session('status.role')) active @endif">
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_roles' )])
        @can('roles.create')
        @slot('tool')
        <div class="box-tools">
            <a class="btn btn-block btn-primary" href="{{action('\Modules\Superadmin\Http\Controllers\DefaultRoleController@create')}}">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
        </div>
        @endslot
        @endcan
        @can('roles.view')
        <table class="table table-bordered table-striped" id="roles_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'user.roles' )</th>
                    <th>@lang( 'messages.action' )</th>
                </tr>
            </thead>
        </table>
        @endcan
        @endcomponent

    </section>
    <!-- /.content -->
</div>