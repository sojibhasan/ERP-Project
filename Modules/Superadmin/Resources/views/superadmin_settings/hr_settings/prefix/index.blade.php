<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\PrefixController@store'), 'method' =>
    'post', 'id' => 'prefix_form' ]) !!}

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'hr::lang.all_your_prefixes' )])
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                {!! Form::label('employee_prefix', __('hr::lang.employee_prefix'), []) !!}
                {!! Form::text('employee_prefix', !empty($prefixes->employee_prefix) ? $prefixes->employee_prefix : null
                , ['class'
                =>
                'form-control', 'placeholder' => __('hr::lang.employee_prefix')]) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('employee_starting_number', __('hr::lang.employee_starting_number'), []) !!}
                {!! Form::text('employee_starting_number', !empty($prefixes->employee_starting_number) ?
                $prefixes->employee_starting_number :
                null , ['class' =>
                'form-control', 'placeholder' => __('hr::lang.employee_starting_number')]) !!}
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