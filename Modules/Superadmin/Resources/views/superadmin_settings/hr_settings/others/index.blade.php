<div class="pos-tab-content @if(session('status.tab') == 'others') active @endif">
    <!-- Main content -->
    <section class="content">
        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\HrSettingsController@store'), 'method' =>
        'post', 'id' => 'prefix_form' ]) !!}

        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'hr::lang.all_your_settings' )])
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    {!! Form::label('overtime_rate', __('hr::lang.overtime_rate'), []) !!}
                    {!! Form::text('overtime_rate', !empty($settings) ? $settings->overtime_rate : null , ['class' =>
                    'form-control', 'placeholder' => __('hr::lang.overtime_rate')]) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('late_time_rate', __('hr::lang.late_time_rate'), []) !!}
                    {!! Form::text('late_time_rate', !empty($settings) ? $settings->late_time_rate : null , ['class' =>
                    'form-control', 'placeholder' => __('hr::lang.late_time_rate')]) !!}
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        @endcomponent
        <div class="col-md-12">
            <button class="btn btn-primary pull-right">@lang('messages.save')</button>
        </div>
        {!! Form::close() !!}
    </section>
</div>