@extends('layouts.member')
@section('title', __('member::lang.home'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('member::lang.profile' ) }} </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberController@update',
            $member->id), 'method' => 'PUT', 'id' => 'member_form' ])
            !!}
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.profile')])

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_name', __('business.name') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('name', $member->name, ['class' => 'form-control','placeholder' =>
                        __('business.name'),
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_address', __('business.address') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('address', $member->address, ['class' => 'form-control','placeholder' =>
                        __('business.address'),
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_town', __('business.town') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('town', $member->town, ['class' => 'form-control','placeholder' =>
                        __('business.town'),
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_district', __('business.district') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('district', $member->district, ['class' => 'form-control','placeholder' =>
                        __('business.district'),
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_mobile_number_1', __('business.mobile_number_1') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('mobile_number_1', $member->mobile_number_1, ['class' =>
                        'form-control','placeholder' =>
                        __('business.mobile_number_1'),
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_mobile_number_2', __('business.mobile_number_2') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('mobile_number_2', $member->mobile_number_2, ['class' =>
                        'form-control','placeholder' =>
                        __('business.mobile_number_2')
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_mobile_number_3', __('business.mobile_number_3') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('mobile_number_3', $member->mobile_number_3, ['class' =>
                        'form-control','placeholder' =>
                        __('business.mobile_number_3')
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_land_number', __('business.land_number') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('land_number', $member->land_number, ['class' => 'form-control','placeholder' =>
                        __('business.land_number')
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_gender', __('business.gender') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('gender', ['male' => 'Male', 'female' => 'Female'], $member->gender, ['class'
                        =>
                        'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_date_of_birth', __('business.date_of_birth') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('date_of_birth', !empty($member->date_of_birth) ?
                        \Carbon::parse($member->date_of_birth)->format('m/d/Y') : null, ['class' =>
                        'form-control','placeholder' =>
                        __('business.date_of_birth'), 'id' => 'date_of_birth'
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('gramasevaka_area', __('business.gramasevaka_area') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('gramasevaka_area', $gramasevaka_areas, $member->gramasevaka_area, ['class'
                        => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('bala_mandalaya_area', __('business.bala_mandalaya_area') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, $member->bala_mandalaya_area,
                        ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' =>
                        'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_group', __('business.member_group') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('member_group', $member_groups, $member->member_group,
                        ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' =>
                        'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-danger pull-right"> @lang('messages.update')</button>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')

<script>
    $('#date_of_birth').datepicker({
          format: 'mm/dd/yyyy'
      });
</script>
@endsection