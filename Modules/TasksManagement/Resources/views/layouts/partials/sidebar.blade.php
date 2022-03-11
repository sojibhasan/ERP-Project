<li class="treeview {{ in_array($request->segment(1), ['tasks-management']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-list-alt"></i> <span>@lang('tasksmanagement::lang.tasks_management')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        @if($notes_page)
        <li class="{{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'notes' ? 'active' : '' }}">
            <a href="{{action('\Modules\TasksManagement\Http\Controllers\NoteController@index')}}"><i
                    class="fa fa-file-text-o"></i>@lang('tasksmanagement::lang.notes')</a>
        </li>
        @endif
        @if($tasks_page)
            @can('tasks_management.tasks')
            <li class="{{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'tasks' ? 'active' : '' }}">
                <a href="{{action('\Modules\TasksManagement\Http\Controllers\TaskController@index')}}"><i
                        class="fa fa-check"></i>@lang('tasksmanagement::lang.list_tasks')</a>
            </li>
            @endcan
        @endif
        @if($reminder_page)
            @can('tasks_management.reminder')
                <li
                class="{{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'reminders' ? 'active' : '' }}">
                <a href="{{action('\Modules\TasksManagement\Http\Controllers\ReminderController@index')}}"><i
                    class="fa fa-bell-o"></i>@lang('tasksmanagement::lang.reminders')</a>
                </li>
            @endcan
        @endif
        <li
            class="{{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'settings' ? 'active' : '' }}">
            <a href="{{action('\Modules\TasksManagement\Http\Controllers\SettingsController@index')}}"><i
                    class="fa fa-cogs"></i>@lang('tasksmanagement::lang.settings')</a>
        </li>

    </ul>
</li>