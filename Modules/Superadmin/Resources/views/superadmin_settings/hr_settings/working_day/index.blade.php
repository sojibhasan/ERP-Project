<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title"><label>@lang('hr::lang.set_working_days')</label></h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(['url' =>
                            action('\Modules\HR\Http\Controllers\WorkingDayController@store'),
                            'method' => 'post']) !!}
                            @foreach ($working_days as $day)
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox($day->days, 1,$day->flag,
                                        [ 'class' => 'input-icheck']); !!} {{$day->days }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md 12">
                                    <button type="submit" class="btn btn-primary pull-right"
                                        style="margin-right: 20px;">Save</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>