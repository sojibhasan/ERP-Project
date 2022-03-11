<div class="pos-tab-content @if(session('status.manage_user')) active @endif">
    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_users' )])
        @can('user.create')
        @slot('tool')
        <div class="box-tools">
            <a class="btn btn-block btn-primary"
                href="{{action('\Modules\Superadmin\Http\Controllers\DefaultManageUserController@create')}}">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
        </div>
        @endslot
        @endcan
        @can('user.view')
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="users_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang( 'business.username' )</th>
                        <th>@lang( 'user.name' )</th>
                        <th>@lang( 'user.role' )</th>
                        <th>@lang( 'business.email' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
        @endcan
        @endcomponent

        <div class="modal fade user_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
</div>